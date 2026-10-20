<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\Meta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SeoController extends Controller
{
    private const PAGES = [
        'home' => 'Home', 'destinations' => 'Study destinations', 'universities' => 'Universities',
        'programs' => 'Academic programs', 'scholarships' => 'Scholarships', 'services' => 'Services',
        'courses' => 'Online courses', 'consultations' => 'Consultations', 'events' => 'Events',
        'blog' => 'Blog and guides', 'about' => 'About us', 'contact' => 'Contact us',
    ];

    public function index()
    {
        $meta = Meta::whereIn('slug', array_keys(self::PAGES))->get()->keyBy('slug');
        return view('admin.cms.seo.index', ['pages' => self::PAGES, 'meta' => $meta]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'pages' => ['required', 'array'],
            'pages.*.meta_title' => ['nullable', 'string', 'max:255'],
            'pages.*.meta_description' => ['nullable', 'string', 'max:500'],
            'pages.*.meta_keyword' => ['nullable', 'string', 'max:500'],
            'pages.*.og_image' => ['nullable', 'integer', Rule::exists('file_managers', 'id')],
        ]);
        DB::transaction(function () use ($data) {
            foreach (self::PAGES as $slug => $pageName) {
                $values = $data['pages'][$slug] ?? [];
                Meta::updateOrCreate(['slug' => $slug], [
                    'page_name' => $pageName,
                    'meta_title' => trim((string) ($values['meta_title'] ?? '')) ?: null,
                    'meta_description' => trim((string) ($values['meta_description'] ?? '')) ?: null,
                    'meta_keyword' => trim((string) ($values['meta_keyword'] ?? '')) ?: null,
                    'og_image' => $values['og_image'] ?? null,
                ]);
            }
        });
        return back()->with('success', __('SEO metadata saved.'));
    }
}
