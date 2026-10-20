<div class="zSidebar">
    <div class="zSidebar-overlay"></div>
    <div class="zSidebar-wrap h-100">
        <x-genz.logo :href="route('content.dashboard')" :inverse="true" :compact="true" class="zSidebar-logo" />
        <x-genz.sidebar-toggle />
        <div class="zSidebar-fixed">
            <ul class="zSidebar-menu" id="sidebarMenu">
                <li><a href="{{ route('content.dashboard') }}" class="d-flex align-items-center cg-10 active"><div class="d-flex"><i class="fa-solid fa-house" aria-hidden="true"></i></div><span>{{ __('Overview') }}</span></a></li>
                <li><a href="{{ route('content.seo.index') }}" class="d-flex align-items-center cg-10 {{ request()->routeIs('content.seo.*') ? 'active' : '' }}"><div class="d-flex"><i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i></div><span>{{ __('SEO settings') }}</span></a></li>
            </ul>
            <x-genz.sidebar-logout />
        </div>
    </div>
</div>
