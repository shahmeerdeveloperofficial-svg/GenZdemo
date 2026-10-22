<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>

    <script>
        (function () {
            var theme = 'light';
            var collapsed = false;
            try {
                theme = localStorage.getItem('genz-admin-theme') === 'dark' ? 'dark' : 'light';
                collapsed = localStorage.getItem('genz-sidebar-collapsed') === 'true';
            } catch (error) {}
            document.documentElement.setAttribute('data-genz-theme', theme);
            if (collapsed) document.documentElement.classList.add('genz-sidebar-collapsed');
        })();
    </script>

    <title>{{ config('genz.name') }} - @stack('title' ?? '')</title>
    @hasSection('meta')
        @stack('meta')
    @else
        @php
            $metaData = getMeta(getMetaSlugForCurrentPage());
        @endphp

            <!-- Open Graph meta tags for social sharing -->
        <meta property="og:type" content="website">
        <meta property="og:title" content="{{ $metaData['meta_title'] ?? config('genz.name') }}">
        <meta property="og:description" content="{{ $metaData['meta_description'] ?? config('genz.tagline') }}">
        <meta property="og:image" content="{{ $metaData['og_image'] ?? getSettingImage('app_logo_black') }}">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="{{ config('genz.name') }}">
        <meta name="description" content="{{ $metaData['meta_description'] }}">
        @if ($metaData['meta_keyword'])<meta name="keywords" content="{{ $metaData['meta_keyword'] }}">@endif
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Twitter Card meta tags for Twitter sharing -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $metaData['meta_title'] ?? config('genz.name') }}">
        <meta name="twitter:description" content="{{ $metaData['meta_description'] ?? config('genz.tagline') }}">
        <meta name="twitter:image" content="{{ $metaData['og_image'] ?? getSettingImage('app_logo_black') }}">

        <meta name="csrf-token" content="{{ csrf_token() }}"/>
    @endif

    <!-- Place favicon.ico in the root directory -->
    <link rel="icon" href="{{ getSettingImage('app_fav_icon') }}" type="image/png" sizes="16x16">
    <link rel="shortcut icon" href="{{ getSettingImage('app_fav_icon') }}" type="image/x-icon">
    <link rel="shortcut icon" href="{{ getSettingImage('app_fav_icon') }}">

    <!-- css file  -->
    <link rel="stylesheet" href="{{asset('assets/css/bootstrap.min.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/css/plugins.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/css/dataTables.css')}}"/>
    <link rel="stylesheet" href="{{asset('assets/css/dataTables.responsive.min.css')}}"/>
    @if (request()->is('admin/cms/*', 'admin/cms-setting/*', 'admin/service/*', 'admin/subject*', 'admin/events*', 'admin/consultations*', 'admin/courses/programs*', 'admin/setting/email-edit*', 'consultant/consultant-profile'))
        <link rel="stylesheet" href="{{asset('assets/css/summernote/summernote-lite.min.css')}}"/>
    @endif
    <link rel="stylesheet" href="{{asset('assets/scss/style.css')}}?v={{env('VERSION', 1)}}"/>
    <link rel="stylesheet" href="{{ asset('genz/css/theme.css') }}?v=5"/>
    @include('layouts.dynamic-color')
    @if (request()->is('admin', 'admin/*', 'consultant', 'consultant/*', 'student', 'student/*', 'affiliate', 'affiliate/*', 'instructor', 'instructor/*', 'finance', 'finance/*', 'content', 'content/*', 'login', 'register', 'password/*'))
        <link rel="stylesheet" href="{{ asset('genz/css/backend.css') }}?v={{ filemtime(public_path('genz/css/backend.css')) }}"/>
    @endif
    @stack('style')
    @if (!request()->is('admin*', 'student*', 'consultant*', 'affiliate*', 'finance*', 'instructor*', 'content*', 'login', 'register', 'password/*'))
        <link rel="stylesheet" href="{{ asset('genz/css/public-navigation.css') }}?v=1"/>
    @endif
    @if(getOption('google_analytics_status', 0))
        <!-- Google tag (gtag.js) -->
        <script async
                src="https://www.googletagmanager.com/gtag/js?id={{ getOption('google_analytics_tracking_id') }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];

            function gtag() {
                dataLayer.push(arguments);
            }

            gtag('js', new Date());
            gtag('config', "{{ getOption('google_analytics_tracking_id') }}");
        </script>
    @endif
</head>
