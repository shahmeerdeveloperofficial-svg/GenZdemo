<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\PublicSiteContent;
use App\Services\PublicSitePayload;

class PublicSiteController extends Controller
{
    public function index(PublicSiteContent $content, PublicSitePayload $payload)
    {
        $manifestPath = public_path('site/manifest.json');
        if (!is_file($manifestPath)) $manifestPath = public_path('site/.vite/manifest.json');
        abort_unless(is_file($manifestPath), 503, 'The website assets need to be built.');
        $manifest = json_decode(file_get_contents($manifestPath), true, 512, JSON_THROW_ON_ERROR);
        $entry = collect($manifest)->first(fn ($item) => !empty($item['isEntry']));
        abort_unless($entry && !empty($entry['file']), 503, 'The website entry asset is missing.');
        $site = $payload->make($content);
        $meta = getMeta('home');
        if (data_get($site, 'content.brand.seoSource', 'backend') === 'custom') {
            $meta['meta_title'] = data_get($site, 'content.brand.pageTitle') ?: $meta['meta_title'];
            $meta['meta_description'] = data_get($site, 'content.brand.description') ?: $meta['meta_description'];
        }
        $favicon = data_get($site, 'content.brand.favicon') ?: getSettingImage('app_fav_icon');
        if (str_starts_with($favicon, '/assets/')) $favicon = '/site' . $favicon;

        return response()->view('frontend.site', [
            'site' => $site,
            'entry' => $entry,
            'meta' => $meta,
            'favicon' => $favicon,
        ])->header('Cache-Control', 'private, no-store');
    }
}
