<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Services\PublicSiteContent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PublicSiteController extends Controller
{
    public function edit(PublicSiteContent $site)
    {
        return view('admin.cms.public-site', [
            'pageTitle' => __('Public website editor'),
            'landingPage' => 'active',
            'showCmsSettings' => 'show',
            'subPublicSiteCmsSettingsActiveClass' => 'active',
            'siteContent' => $site->content(),
            'siteDefaults' => $site->defaults(),
            'siteSchema' => $site->editorSchema(),
        ]);
    }

    public function update(Request $request, PublicSiteContent $site)
    {
        $validated = $request->validate(['content' => ['required', 'array']]);
        $content = $site->save($validated['content']);

        return response()->json([
            'message' => __('Website changes saved. Refresh the public website to see them.'),
            'content' => $content,
        ]);
    }

    public function media(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,gif,pdf', 'max:10240'],
        ]);
        $file = $request->file('file');
        $extensions = [
            'image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp',
            'image/gif' => 'gif', 'application/pdf' => 'pdf',
        ];
        abort_unless(isset($extensions[$file->getMimeType()]), 422, 'Choose a JPG, PNG, WebP, GIF or PDF file.');
        $basename = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) ?: 'website-media';
        $filename = Str::limit($basename, 90, '') . '-' . Str::random(16) . '.' . $extensions[$file->getMimeType()];
        $directory = public_path('uploads/public-site');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        $file->move($directory, $filename);

        return response()->json([
            'message' => __('File uploaded. Save website changes to publish it.'),
            'url' => '/uploads/public-site/' . $filename,
            'name' => $file->getClientOriginalName(),
        ], 201);
    }
}
