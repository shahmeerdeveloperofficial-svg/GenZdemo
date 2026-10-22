@extends('layouts.app')
@push('title'){{ $pageTitle }}@endpush
@push('style')
<style>
    .site-editor { --site-border:var(--genz-border,#dce6f1); --site-surface:var(--genz-surface,#fff); --site-text:var(--genz-text,#142945); }
    .site-editor .site-panel,.site-editor .site-group,.site-editor .site-object,.site-editor .site-list-item { border:1px solid var(--site-border); border-radius:12px; background:var(--site-surface); color:var(--site-text); }
    .site-editor .site-panel { padding:20px; }
    .site-editor .site-group { margin-bottom:14px; overflow:hidden; }
    .site-editor summary { padding:16px 18px; cursor:pointer; font-weight:700; }
    .site-editor summary::marker { color:#16bcf3; }
    .site-editor .site-group-body { padding:4px 18px 20px; }
    .site-editor .site-fields { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:16px; }
    .site-editor .site-field { min-width:0; }
    .site-editor .site-field > label { display:block; font-weight:600; margin-bottom:7px; }
    .site-editor .site-wide { grid-column:1/-1; }
    .site-editor .site-object { padding:15px; margin:0; min-width:0; }
    .site-editor .site-object > legend { float:none; width:auto; font-size:15px; padding:0 5px; font-weight:700; }
    .site-editor .site-list { display:grid; gap:12px; }
    .site-editor .site-list-item { padding:14px; }
    .site-editor .site-item-heading { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px; margin-bottom:12px; }
    .site-editor .site-item-actions { display:flex; gap:6px; }
    .site-editor .site-small-button { border:1px solid var(--site-border); background:transparent; color:inherit; border-radius:7px; padding:5px 9px; }
    .site-editor .site-small-button:disabled { opacity:.4; }
    .site-editor .site-remove { color:#d83c55; }
    .site-editor .site-add { justify-self:start; border:1px dashed #16bcf3; }
    .site-editor .site-check { display:flex; align-items:center; gap:9px; min-height:42px; }
    .site-editor .site-media-row { display:flex; gap:8px; margin-top:8px; align-items:center; flex-wrap:wrap; }
    .site-editor .site-preview { max-width:140px; max-height:85px; border:1px solid var(--site-border); border-radius:7px; object-fit:contain; background:#eef4fa; }
    .site-editor .site-save-bar { position:sticky; bottom:12px; z-index:4; display:flex; justify-content:space-between; gap:12px; align-items:center; flex-wrap:wrap; padding:14px 18px; margin-top:20px; border:1px solid var(--site-border); border-radius:12px; background:var(--site-surface); box-shadow:0 5px 24px #07357915; }
    .site-editor .site-status { margin:0; font-size:14px; }
    .site-editor .site-status[data-kind=error] { color:#d83c55; white-space:pre-line; }
    .site-editor .site-status[data-kind=success] { color:#16814d; }
    .site-editor .site-invalid { border-color:#d83c55!important; }
    .site-editor .site-catalog-links { display:flex; flex-wrap:wrap; gap:8px; }
    .site-editor .site-help { font-size:13px; opacity:.76; margin:5px 0 10px; }
    html[data-genz-theme=dark] .site-editor { --site-border:#2d455f; --site-surface:#15263a; --site-text:#e5edf7; }
    html[data-genz-theme=dark] .site-editor input,html[data-genz-theme=dark] .site-editor textarea,html[data-genz-theme=dark] .site-editor select { background:#0f1e30; color:#e5edf7; border-color:#2d455f; }
    @media(max-width:767px) { .site-editor .site-fields { grid-template-columns:1fr; } .site-editor .site-panel { padding:14px; } .site-editor .site-group-body { padding:4px 12px 16px; } }
</style>
@endpush
@section('content')
<div class="p-sm-30 p-15 site-editor" id="public-site-editor"
     data-save-url="{{ route('admin.cms-settings.public-site.update') }}"
     data-media-url="{{ route('admin.cms-settings.public-site.media') }}">
    <header class="mb-4">
        <div class="d-flex flex-wrap gap-3 justify-content-between align-items-start">
            <div><p class="text-muted mb-1">{{ __('Website content and appearance') }}</p><h1 class="fs-24 fw-700">{{ $pageTitle }}</h1></div>
            <a href="{{ route('frontend') }}" target="_blank" rel="noopener" class="genz-button-secondary">{{ __('Open website') }}</a>
        </div>
        <p class="mt-2 mb-0">{{ __('Open a section to edit its text, images, links and cards. Enabled switches show or hide sections; a lower order number moves a section higher on the page.') }}</p>
    </header>
    <div class="row g-3">
        <aside class="col-xl-3"><div class="site-panel">@include('admin.cms.partials.cms-sidebar')</div></aside>
        <div class="col-xl-9">
            <section class="site-panel mb-3">
                <h2 class="h6 mb-2">{{ __('Manage website records') }}</h2>
                <p class="site-help">{{ __('Published records below can supply website cards. Content-source controls in each section choose which records appear.') }}</p>
                <div class="site-catalog-links">
                    <a class="site-small-button" href="{{ route('admin.services.index') }}">{{ __('Services') }}</a>
                    <a class="site-small-button" href="{{ route('admin.cms-settings.countries.index') }}">{{ __('Countries') }}</a>
                    <a class="site-small-button" href="{{ route('admin.cms-settings.universities.index') }}">{{ __('Universities') }}</a>
                    <a class="site-small-button" href="{{ route('admin.cms-settings.blogs.index') }}">{{ __('Blogs') }}</a>
                    <a class="site-small-button" href="{{ route('admin.events.index') }}">{{ __('Events') }}</a>
                    <a class="site-small-button" href="{{ route('admin.cms-settings.testimonials.index') }}">{{ __('Testimonials') }}</a>
                    <a class="site-small-button" href="{{ route('admin.cms-settings.faqs.index') }}">{{ __('FAQs') }}</a>
                    <a class="site-small-button" href="{{ route('admin.seo.index') }}">{{ __('SEO') }}</a>
                    <a class="site-small-button" href="{{ route('admin.cms-settings.public-site.subscribers') }}">{{ __('Newsletter subscribers') }}</a>
                </div>
            </section>
            <form id="public-site-form" data-no-loading>
                @csrf
                <div id="public-site-fields"></div>
                <noscript><div class="alert alert-warning">{{ __('Enable JavaScript to use the website editor.') }}</div></noscript>
                <div class="site-save-bar">
                    <p class="site-status" id="public-site-status" aria-live="polite">{{ __('Changes appear on the website after you save.') }}</p>
                    <button type="submit" class="genz-button-primary" id="public-site-save">{{ __('Save website changes') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="application/json" id="public-site-data">{!! json_encode(['content' => $siteContent, 'defaults' => $siteDefaults, 'schema' => $siteSchema], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE) !!}</script>
@endsection
@push('script')
<script src="{{ asset('admin/custom/js/public-site.js') }}?v={{ filemtime(public_path('admin/custom/js/public-site.js')) }}"></script>
@endpush
