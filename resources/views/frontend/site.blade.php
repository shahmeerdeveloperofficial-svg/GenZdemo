<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ selectedLanguage()?->rtl == 1 ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $meta['meta_title'] }}</title>
    <meta name="description" content="{{ $meta['meta_description'] }}">
    @if($meta['meta_keyword'])<meta name="keywords" content="{{ $meta['meta_keyword'] }}">@endif
    <link rel="canonical" href="{{ route('frontend') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $meta['meta_title'] }}">
    <meta property="og:description" content="{{ $meta['meta_description'] }}">
    <meta property="og:image" content="{{ $meta['og_image'] }}">
    <meta property="og:url" content="{{ route('frontend') }}">
    <meta name="twitter:card" content="summary_large_image">
    <link rel="icon" href="{{ $favicon }}">
    @foreach($entry['css'] ?? [] as $stylesheet)<link rel="stylesheet" href="{{ asset('site/' . $stylesheet) }}">@endforeach
    <link rel="stylesheet" href="{{ asset('genz/css/public-navigation.css') }}?v=1">
    <script>window.__GENZ_SITE__ = {{ Illuminate\Support\Js::from($site) }};</script>
    @if(getOption('google_analytics_status', 0) && getOption('google_analytics_tracking_id'))
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ getOption('google_analytics_tracking_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag() { dataLayer.push(arguments); }
            gtag('js', new Date());
            gtag('config', @json(getOption('google_analytics_tracking_id')));
        </script>
    @endif
</head>
<body>
    <div id="root"></div>
    <noscript>
        <main style="max-width:900px;margin:4rem auto;padding:1rem;font-family:system-ui">
            <h1>{{ data_get($site, 'content.brand.name', config('genz.name')) }}</h1>
            <p>{{ $meta['meta_description'] }}</p>
            <p>{{ __('Enable JavaScript to view the interactive website. You can still use the following pages:') }}</p>
            <nav><a href="{{ route('study-abroad-registration.create') }}">{{ __('Student registration') }}</a> · <a href="{{ route('academic-programs.list') }}">{{ __('Programs') }}</a> · <a href="{{ route('contact-us') }}">{{ __('Contact') }}</a> · <a href="{{ route('login') }}">{{ __('Portal login') }}</a></nav>
        </main>
    </noscript>
    <script type="module" src="{{ asset('site/' . $entry['file']) }}"></script>
    @if((int) getOption('cookie_status', 0) === STATUS_ACTIVE)
        <style>
            .genz-site-cookie .js-cookie-consent{z-index:1000;background:#fff;color:#142945;border-top:1px solid #e3eaf3;padding:12px 20px;box-shadow:0 -4px 20px #07357915}
            .genz-site-cookie .cookie-consent__message{font-size:14px;margin:0 20px 0 0}
            .genz-site-cookie .js-cookie-consent-agree{padding:10px 18px;background:#073579;color:white;border:0;border-radius:9px}
            .genz-site-cookie .js-cookie-consent .hidden{display:block}
        </style>
        <div class="genz-site-cookie">@include('cookie-consent::index')</div>
    @endif
</body>
</html>
