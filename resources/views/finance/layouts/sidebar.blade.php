<div class="zSidebar">
    <div class="zSidebar-overlay"></div>
    <div class="zSidebar-wrap h-100">
        <x-genz.logo :href="route('finance.dashboard')" :inverse="true" :compact="true" class="zSidebar-logo" />
        <x-genz.sidebar-toggle />
        <div class="zSidebar-fixed">
            <ul class="zSidebar-menu" id="sidebarMenu">
                <li><a href="{{ route('finance.dashboard') }}" class="d-flex align-items-center cg-10 active"><div class="d-flex"><i class="fa-solid fa-chart-pie" aria-hidden="true"></i></div><span>{{ __('Overview') }}</span></a></li>
            </ul>
            <x-genz.sidebar-logout />
        </div>
    </div>
</div>
