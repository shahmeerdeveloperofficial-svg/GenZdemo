<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\Country;
use App\Models\Event;
use App\Models\FileManager;
use App\Models\Service;
use App\Models\Testimonial;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PublicSitePayload
{
    private array $media = [];
    private array $mediaDisks = [];

    public function make(PublicSiteContent $content): array
    {
        $dashboard = auth()->check() && Route::has(getPathByRole() . '.dashboard')
            ? route(getPathByRole() . '.dashboard', [], false)
            : route('login', [], false);
        $routeNames = [
            'home' => 'frontend', 'registration' => 'study-abroad-registration.create',
            'register' => 'register', 'login' => 'login', 'forgotPassword' => 'password.request',
            'contact' => 'contact-us', 'blogs' => 'blog.list', 'events' => 'event.list',
            'courses' => 'courses.list', 'consultations' => 'consultations.list',
            'academicPrograms' => 'academic-programs.list', 'universities' => 'universities.list',
            'subjects' => 'subject.list',
            'about' => 'about-us.details', 'scholarships' => 'scholarship.list',
        ];
        $routes = [];
        foreach ($routeNames as $key => $name) $routes[$key] = route($name, [], false);
        $routes += [
            'dashboard' => $dashboard,
            'privacy' => route('page', ['slug' => 'privacy_policy_page'], false),
            'terms' => route('page', ['slug' => 'terms_services_page'], false),
            'refund' => route('page', ['slug' => 'refund_policy_page'], false),
        ];

        $siteContent = $content->content();
        // Established Laravel destinations come first; retain unique CMS menu links after them.
        $canonicalLinks = [
            ['label' => __('Home'), 'route' => 'home'],
            ['label' => __('About Us'), 'route' => 'about'],
            ['label' => __('Programs'), 'route' => 'academicPrograms'],
            ['label' => __('Universities'), 'route' => 'universities'],
            ['label' => __('Subjects'), 'route' => 'subjects'],
            ['label' => __('Scholarships'), 'route' => 'scholarships'],
            ['label' => __('Courses'), 'route' => 'courses'],
            ['label' => __('Consultations'), 'route' => 'consultations'],
            ['label' => __('Events'), 'route' => 'events'],
            ['label' => __('Blogs'), 'route' => 'blogs'],
            ['label' => __('Contact Us'), 'route' => 'contact'],
        ];
        $canonicalRoutes = array_column($canonicalLinks, 'route');
        $canonicalLabels = ['home', 'about', 'about us', 'programs', 'universities', 'subjects', 'scholarships', 'scholarship', 'courses', 'consultations', 'events', 'news', 'blogs', 'contact', 'contact us', 'registration', 'student registration'];
        $customLinks = array_values(array_filter(
            data_get($siteContent, 'navigation.links', []),
            fn ($link) => is_array($link) && isset($link['label'])
                && (!isset($link['route']) || (!in_array($link['route'], $canonicalRoutes, true) && $link['route'] !== 'registration'))
                && !in_array(mb_strtolower(trim($link['label'])), $canonicalLabels, true)
        ));
        data_set($siteContent, 'navigation.links', array_merge($canonicalLinks, $customLinks));

        return [
            'content' => $siteContent,
            'routes' => $routes,
            'forms' => [
                'inquiry' => route('site.inquiries.store', [], false),
                'newsletter' => route('site.newsletter.store', [], false),
            ],
            'csrf' => csrf_token(),
            'auth' => [
                'authenticated' => auth()->check(),
                'name' => auth()->user()?->name,
                'dashboard' => $dashboard,
            ],
            'registration' => ['branches' => config('genz.registration.branches', [])],
            'recaptcha' => [
                'enabled' => (int) getOption('recapture_in_contact_us', 0) === 1,
                'siteKey' => (string) getOption('google_recaptcha_site_key', ''),
            ],
            'catalog' => $this->catalog(),
        ];
    }

    private function catalog(): array
    {
        $events = Event::where('status', STATUS_ACTIVE)
            ->where('date_time', '>=', now()->startOfDay())->orderBy('date_time')->limit(3)->get();
        $blogs = Blog::where('status', STATUS_ACTIVE)
            ->where(fn ($q) => $q->whereNull('publish_date')->orWhere('publish_date', '<=', now()))
            ->latest('publish_date')->limit(3)->get();
        $services = Service::where('status', STATUS_ACTIVE)->orderBy('title')->get();
        $countries = Country::where('status', STATUS_ACTIVE)->orderBy('name')->get();
        $testimonials = Testimonial::where('status', STATUS_ACTIVE)->latest()->limit(12)->get();
        $fileIds = $events->pluck('image')->merge($blogs->pluck('banner_image'))
            ->merge($services->pluck('image'))->merge($countries->pluck('banner_image'))
            ->merge($testimonials->pluck('image'))->filter()->unique();
        // Only website media disks are eligible; student/private storage is excluded.
        foreach (['public', 'aws', 'Wasabi', 'vultr', 'do', 's3', 'wasabi'] as $disk) {
            if (config("filesystems.disks.$disk")) $this->mediaDisks[strtolower($disk)] = $disk;
        }
        $this->media = FileManager::whereIn('id', $fileIds)->get()
            ->filter(fn ($file) => isset($this->mediaDisks[strtolower((string) $file->storage_type)]))
            ->keyBy('id')->all();
        $news = $events->map(fn ($event) => [
            'id' => 'event-' . $event->id, 'title' => $event->title,
            'date' => $this->date($event->date_time), 'location' => $event->location ?: __('Online'),
            'category' => __('Event'), 'excerpt' => $this->plain($event->description),
            'image' => $this->image($event->image), 'url' => route('event.details', $event->slug, false),
        ])->concat($blogs->map(fn ($blog) => [
            'id' => 'blog-' . $blog->id, 'title' => $blog->title,
            'date' => $this->date($blog->publish_date), 'location' => '',
            'category' => __('Article'), 'excerpt' => $this->plain($blog->details),
            'image' => $this->image($blog->banner_image), 'url' => route('blog.details', $blog->slug, false),
        ]))->values()->all();

        return [
            'newsEvents' => $news,
            'services' => $services->map(fn ($service) => [
                'id' => $service->id, 'title' => $service->title,
                'description' => $this->plain($service->description),
                'image' => $this->image($service->image), 'url' => route('service.details', $service->slug, false),
            ])->values()->all(),
            'countries' => $countries->map(fn ($country) => [
                'id' => $country->id, 'name' => $country->name, 'title' => $country->name,
                'description' => $this->plain($country->details),
                'image' => $this->image($country->banner_image), 'url' => route('country.details', $country->slug, false),
            ])->values()->all(),
            'testimonials' => $testimonials->map(fn ($review) => [
                'id' => 'review-' . $review->id, 'studentName' => $review->name,
                'batch' => __('Student feedback'), 'university' => '', 'country' => '',
                'program' => '', 'scholarshipTier' => '', 'avatar' => $this->image($review->image),
                'quote' => $this->plain($review->description, 1200),
            ])->values()->all(),
        ];
    }

    private function image($id): string
    {
        $fallback = asset('assets/images/no-image.jpg');
        $file = $this->media[$id] ?? null;
        if (!$file || !$file->path) return $fallback;
        $disk = $this->mediaDisks[strtolower((string) $file->storage_type)] ?? null;
        if (!$disk) return $fallback;
        try {
            if ($disk === 'public') {
                return Storage::disk('public')->exists($file->path) ? asset('storage/' . $file->path) : $fallback;
            }
            return Storage::disk($disk)->url($file->path);
        } catch (\Throwable) {
            // An unconfigured cloud disk must not prevent the public homepage from rendering.
            return $fallback;
        }
    }

    private function plain(?string $value, int $length = 220): string
    {
        return Str::limit(trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($value ?? ''), ENT_QUOTES, 'UTF-8'))), $length);
    }

    private function date(?string $value): string
    {
        if (!$value) return '';
        try {
            return Carbon::parse($value)->format('M j, Y');
        } catch (\Throwable) {
            return '';
        }
    }
}
