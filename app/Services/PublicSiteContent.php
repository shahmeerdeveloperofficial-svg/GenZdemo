<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use JsonException;
use RuntimeException;

/**
 * Only the public website's explicitly defined content is exposed and editable.
 * Objects merge recursively; an edited list replaces the default list in full.
 */
class PublicSiteContent
{
    public const SETTING_KEY = 'genz_public_site_content';
    private const MAX_BYTES = 1500000;
    private int $nodeCount = 0;
    private array $objectPaths = [];

    public function defaults(): array
    {
        $content = [];
        $this->objectPaths = [];
        $paths = glob(resource_path('site/content/*.json')) ?: [];
        sort($paths);
        foreach ($paths as $path) {
            try {
                $json = file_get_contents($path);
                $groups = json_decode($json, true, 40, JSON_THROW_ON_ERROR);
                $this->recordObjects(json_decode($json, false, 40, JSON_THROW_ON_ERROR));
            } catch (JsonException $exception) {
                throw new RuntimeException('Invalid website content defaults: ' . basename($path), 0, $exception);
            }
            if (!is_array($groups) || array_is_list($groups)) {
                throw new RuntimeException('Website content defaults must contain named groups.');
            }
            $content = $this->merge($content, $groups);
        }
        return $content;
    }

    public function content(): array
    {
        $defaults = $this->defaults();
        $stored = Setting::where('option_key', self::SETTING_KEY)->value('option_value');
        if (!$stored) {
            return $defaults;
        }
        try {
            $saved = json_decode($stored, true, 40, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            report($exception);
            return $defaults;
        }
        return is_array($saved) ? $this->mergeKnown($defaults, $saved) : $defaults;
    }

    public function save(array $content): array
    {
        $defaults = $this->defaults();
        if (!$defaults) {
            throw ValidationException::withMessages(['content' => 'Website content defaults are not available yet.']);
        }
        if (strlen(json_encode($content, JSON_THROW_ON_ERROR)) > self::MAX_BYTES) {
            throw ValidationException::withMessages(['content' => 'The website content is too large.']);
        }
        $this->nodeCount = 0;
        $validated = $this->validateNode($content, $defaults, 'content', 0);
        $complete = $this->merge($defaults, $validated);
        DB::transaction(function () use ($complete) {
            Setting::updateOrCreate(
                ['option_key' => self::SETTING_KEY],
                ['option_value' => json_encode($complete, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR)]
            );
        });
        return $complete;
    }

    /** Metadata retains list templates even after all items are removed. */
    public function editorSchema(): array
    {
        return $this->schemaFor($this->defaults());
    }

    private function recordObjects(mixed $value, string $path = ''): void
    {
        if (is_object($value)) {
            $this->objectPaths[$path] = true;
            foreach (get_object_vars($value) as $key => $child) {
                $this->recordObjects($child, ltrim("$path.$key", '.'));
            }
        } elseif (is_array($value)) {
            foreach ($value as $child) {
                $this->recordObjects($child, "$path.*");
            }
        }
    }

    private function normalizedPath(string $path): string
    {
        return preg_replace('/(?:^|\.)\d+(?=\.|$)/', '.*', preg_replace('/^content\.?/', '', $path));
    }

    private function isObject(array $value, string $path): bool
    {
        return !array_is_list($value) || ($value === [] && isset($this->objectPaths[$this->normalizedPath($path)]));
    }

    private function merge(array $base, array $override): array
    {
        foreach ($override as $key => $value) {
            if (isset($base[$key]) && is_array($base[$key]) && is_array($value)
                && !array_is_list($base[$key]) && !array_is_list($value)) {
                $base[$key] = $this->merge($base[$key], $value);
            } else {
                $base[$key] = $value;
            }
        }
        return $base;
    }

    private function mergeKnown(array $base, array $override): array
    {
        foreach ($base as $key => $default) {
            if (!array_key_exists($key, $override)) {
                continue;
            }
            $value = $override[$key];
            if (is_array($default) && !array_is_list($default) && is_array($value)) {
                $base[$key] = $this->mergeKnown($default, $value);
            } else {
                $base[$key] = $value;
            }
        }
        return $base;
    }

    private function listTemplate(array $items, string $path = ''): mixed
    {
        if (!$items) {
            $emptyTemplates = [
                'contact.branches' => ['name' => '', 'address' => '', 'phone' => ''],
                'contact.socialLinks' => ['label' => '', 'url' => ''],
            ];
            if (isset($emptyTemplates[$this->normalizedPath($path)])) {
                return $emptyTemplates[$this->normalizedPath($path)];
            }
            return '';
        }
        $template = $items[0];
        if (is_array($template) && !array_is_list($template)) {
            foreach (array_slice($items, 1) as $item) {
                if (is_array($item) && !array_is_list($item)) {
                    $template = $this->mergeTemplates($template, $item);
                }
            }
        }
        return $template;
    }

    private function mergeTemplates(array $base, array $other): array
    {
        foreach ($other as $key => $value) {
            if (array_key_exists($key, $base) && is_array($base[$key]) && is_array($value)) {
                $base[$key] = array_is_list($base[$key]) && array_is_list($value)
                    ? array_merge($base[$key], $value)
                    : $this->mergeTemplates($base[$key], $value);
            } elseif (!array_key_exists($key, $base)) {
                $base[$key] = $value;
            }
        }
        return $base;
    }

    private function fieldFormat(string $path, mixed $value = ''): ?string
    {
        $parts = explode('.', $this->normalizedPath($path));
        $key = (string) end($parts);
        $name = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));
        $parent = $parts[count($parts) - 2] ?? '';
        if ($parent === 'media' || preg_match('/(?:^|_)(?:image|avatar|logo|favicon|photo|poster|file|prospectus)(?:_url)?$/', $name)) {
            return 'media';
        }
        if (preg_match('/(?:^|_)(?:url|href|src|video|media|link)$/', $name)
            || (is_string($value) && preg_match('~^(?:/(?!/)|\#|https?://|mailto:|tel:)~i', $value))) {
            return 'url';
        }
        return null;
    }

    private function fieldOptions(string $path): array
    {
        $path = $this->normalizedPath($path);
        if (str_ends_with($path, '.route')) {
            return ['', 'home', 'registration', 'login', 'dashboard', 'academicPrograms', 'universities', 'subjects',
                'courses', 'consultations', 'events', 'blogs', 'contact', 'about', 'scholarships',
                'register', 'forgotPassword', 'privacy', 'terms', 'refund'];
        }
        $options = [
            'brand.seoSource' => ['backend', 'custom'],
            'sections.news.source' => ['backend', 'custom'],
            'sections.stories.source' => ['backend', 'custom'],
            'sections.philosophy.items.*.Icon' => ['Lightbulb', 'Cpu', 'Users', 'Shield'],
            'sections.benefits.items.*.Icon' => ['BookOpen', 'Cpu', 'Plane', 'LayoutDashboard'],
            'sections.ceo.metrics.*.icon' => ['Users', 'Trophy', 'Compass', 'Target'],
            'sections.welcome.cards.*.icon' => ['BookOpenCheck', 'Globe2', 'ShieldCheck', 'Target'],
        ];
        return $options[$path] ?? [];
    }

    private function schemaFor(mixed $value, string $path = ''): array
    {
        if (is_array($value)) {
            if (!$this->isObject($value, $path)) {
                $template = $this->listTemplate($value, $path);
                return ['type' => 'list', 'template' => $template, 'item' => $this->schemaFor($template, "$path.*")];
            }
            $fields = [];
            foreach ($value as $key => $item) {
                $fields[$key] = $this->schemaFor($item, ltrim("$path.$key", '.'));
            }
            return ['type' => 'object', 'fields' => $fields];
        }
        return [
            'type' => is_bool($value) ? 'boolean' : (is_numeric($value) && !is_string($value) ? 'number' : 'string'),
            'format' => $this->fieldFormat($path, $value),
            'options' => $this->fieldOptions($path),
        ];
    }

    private function validateNode(mixed $value, mixed $template, string $path, int $depth): mixed
    {
        if (++$this->nodeCount > 18000 || $depth > 25) {
            $this->invalid($path, 'The content contains too many nested fields.');
        }
        if (is_array($template)) {
            if (!is_array($value)) {
                $this->invalid($path, 'Use a list or group of fields.');
            }
            if (!$this->isObject($template, $path)) {
                if (!array_is_list($value) || count($value) > 150) {
                    $this->invalid($path, 'A list may contain at most 150 items.');
                }
                $itemTemplate = $this->listTemplate($template, $path);
                $result = [];
                foreach ($value as $index => $item) {
                    $result[] = $this->validateNode($item, $itemTemplate, "$path.$index", $depth + 1);
                }
                return $result;
            }
            $result = [];
            foreach ($value as $key => $item) {
                if (!array_key_exists($key, $template)) {
                    $this->invalid("$path.$key", 'This field is not part of the website content.');
                }
                $result[$key] = $this->validateNode($item, $template[$key], "$path.$key", $depth + 1);
            }
            return $result;
        }
        if (is_bool($template)) {
            if (!is_bool($value)) {
                $this->invalid($path, 'Choose enabled or disabled.');
            }
            return $value;
        }
        if (is_int($template) || is_float($template)) {
            if ((!is_int($value) && !is_float($value)) || !is_finite((float) $value) || abs($value) > 1000000000) {
                $this->invalid($path, 'Enter a valid number between -1,000,000,000 and 1,000,000,000.');
            }
            return $value;
        }
        if (!is_string($value) && !is_null($value)) {
            $this->invalid($path, 'Enter text in this field.');
        }
        $value = (string) $value;
        if (mb_strlen($value) > 20000) {
            $this->invalid($path, 'Text must be no longer than 20,000 characters.');
        }
        if (preg_match('/<\s*\/?\s*[a-z][^>]*>/i', $value)) {
            $this->invalid($path, 'Use plain text without HTML tags.');
        }
        if (preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $value)) {
            $this->invalid($path, 'Remove control characters from this field.');
        }
        if (preg_match('/^\s*(javascript|vbscript|data|file)\s*:/i', $value)) {
            $this->invalid($path, 'Use a website link or a site-relative path.');
        }
        $options = $this->fieldOptions($path);
        if ($options && !in_array($value, $options, true)) {
            $this->invalid($path, 'Choose one of the available options.');
        }
        if ($this->fieldFormat($path, $template)) {
            $value = trim($value);
        }
        if ($value !== '' && $this->fieldFormat($path, $template)) {
            if (mb_strlen($value) > 2048 || str_contains($value, '\\') || preg_match('/[\r\n]/', $value)
                || str_starts_with($value, '//')
                || !preg_match('~^(?:/(?!/)|\#|https?://|mailto:|tel:)~i', $value)) {
                $this->invalid($path, 'Use https://, a site path starting with /, #section, mailto: or tel:.');
            }
            if (preg_match('~^https?://~i', $value) && !filter_var($value, FILTER_VALIDATE_URL)) {
                $this->invalid($path, 'Enter a valid website address.');
            }
        }
        return $value;
    }

    private function invalid(string $path, string $message): never
    {
        throw ValidationException::withMessages([$path => $message]);
    }
}
