<!DOCTYPE html>
<html class="no-js" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

@include('layouts.header')

<body
    class="genz-theme genz-admin-shell font-family-nunitoSans {{ selectedLanguage()?->rtl == 1 ? 'direction-rtl' : 'direction-ltr' }} {{ !(getOption('app_color_design_type', DEFAULT_COLOR) == DEFAULT_COLOR) }}">

@if (request()->is('admin', 'admin/*', 'consultant', 'consultant/*', 'student', 'student/*', 'affiliate', 'affiliate/*', 'instructor', 'instructor/*', 'finance', 'finance/*', 'content', 'content/*', 'login', 'register', 'password/*'))
<div id="genz-page-loading" aria-hidden="true">
    <div class="genz-loading-skeleton" role="status" aria-label="{{ __('Loading page') }}">
        <aside class="genz-loading-skeleton__sidebar">
            <div class="genz-loading-skeleton__brand"><span>G</span><i></i></div>
            <div class="genz-loading-skeleton__nav"><i></i><i></i><i></i><i></i><i></i><i></i><i></i></div>
        </aside>
        <main class="genz-loading-skeleton__main">
            <header><i></i><div><b></b><b></b><b></b></div></header>
            <section class="genz-loading-skeleton__content">
                <div class="genz-loading-skeleton__heading"><i></i><b></b></div>
                <div class="genz-loading-skeleton__cards"><i></i><i></i><i></i></div>
                <div class="genz-loading-skeleton__panel"><i></i><i></i><i></i><i></i><i></i></div>
            </section>
        </main>
    </div>
</div>
@endif

<input type="hidden" id="lang_code" value="{{session('local')}}">
@if (getOption('app_preloader_status', 0) == STATUS_ACTIVE && !request()->is('admin', 'admin/*', 'consultant', 'consultant/*', 'student', 'student/*', 'affiliate', 'affiliate/*', 'instructor', 'instructor/*', 'finance', 'finance/*', 'content', 'content/*', 'login', 'register', 'password/*'))
    <div id="preloader">
        <div id="preloader_status">
            <img src="{{ getSettingImage('app_preloader') }}" alt="{{ getOption('app_name') }}" onerror="this.onerror=null;this.src='{{ asset('genz/images/genz-consultants-logo.png') }}';"/>
        </div>
    </div>
@endif
@if(auth()->check() && !isset($disableHeader))
    <div class="zMain-wrap">
        <!-- Sidebar -->
        @include(getPrefix().'.layouts.sidebar')
        <!-- Main Content -->
        <div class="zMainContent">
            <!-- Header -->
            @include('layouts.nav')
            <!-- Content -->
            @yield('content')
        </div>
    </div>
@else
    @yield('content')
@endif

@if (!empty(getOption('cookie_status')) && getOption('cookie_status') == STATUS_ACTIVE)
    <div class="cookie-consent-wrap shadow-lg">
        @include('cookie-consent::index')
    </div>
@endif
@include('layouts.script')
</body>

</html>
