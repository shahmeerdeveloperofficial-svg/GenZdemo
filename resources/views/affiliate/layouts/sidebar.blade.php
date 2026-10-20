<div class="zSidebar">
    <div class="zSidebar-overlay"></div>
    <!--  -->
    <div class="zSidebar-wrap h-100">
        <!-- Logo -->
        <x-genz.logo :href="route('affiliate.dashboard')" :inverse="true" :compact="true" class="zSidebar-logo" />
        <x-genz.sidebar-toggle />
        <!-- Menu & Logout -->
        <div class="zSidebar-fixed">
            <ul class="zSidebar-menu" id="sidebarMenu">
                <li>
                    <a href="{{ route('affiliate.dashboard') }}"
                        class="d-flex align-items-center cg-10 {{ $activeDashboard ?? '' }}">
                        <div class="d-flex">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                fill="none">
                                <path
                                    d="M12 3.75C12 3.05109 12 2.70164 12.1142 2.42597C12.2664 2.05844 12.5585 1.76642 12.926 1.61418C13.2017 1.5 13.5511 1.5 14.25 1.5C14.9489 1.5 15.2983 1.5 15.574 1.61418C15.9415 1.76642 16.2336 2.05844 16.3858 2.42597C16.5 2.70164 16.5 3.05109 16.5 3.75V6.75C16.5 7.44891 16.5 7.79835 16.3858 8.07405C16.2336 8.44155 15.9415 8.7336 15.574 8.88585C15.2983 9 14.9489 9 14.25 9C13.5511 9 13.2017 9 12.926 8.88585C12.5585 8.7336 12.2664 8.44155 12.1142 8.07405C12 7.79835 12 7.44891 12 6.75V3.75Z"
                                    stroke="#FFFFFF" stroke-width="1.5" />
                                <path
                                    d="M12 14.25C12 13.5511 12 13.2017 12.1142 12.926C12.2664 12.5585 12.5585 12.2664 12.926 12.1142C13.2017 12 13.5511 12 14.25 12C14.9489 12 15.2983 12 15.574 12.1142C15.9415 12.2664 16.2336 12.5585 16.3858 12.926C16.5 13.2017 16.5 13.5511 16.5 14.25C16.5 14.9489 16.5 15.2983 16.3858 15.574C16.2336 15.9415 15.9415 16.2336 15.574 16.3858C15.2983 16.5 14.9489 16.5 14.25 16.5C13.5511 16.5 13.2017 16.5 12.926 16.3858C12.5585 16.2336 12.2664 15.9415 12.1142 15.574C12 15.2983 12 14.9489 12 14.25Z"
                                    stroke="#FFFFFF" stroke-width="1.5" />
                                <path
                                    d="M1.5 12C1.5 10.5858 1.5 9.8787 1.93934 9.43935C2.37868 9 3.08579 9 4.5 9H6C7.41421 9 8.1213 9 8.56065 9.43935C9 9.8787 9 10.5858 9 12V13.5C9 14.9142 9 15.6213 8.56065 16.0606C8.1213 16.5 7.41421 16.5 6 16.5H4.5C3.08579 16.5 2.37868 16.5 1.93934 16.0606C1.5 15.6213 1.5 14.9142 1.5 13.5V12Z"
                                    stroke="#FFFFFF" stroke-width="1.5" />
                                <path
                                    d="M1.5 3.75C1.5 3.05109 1.5 2.70164 1.61418 2.42597C1.76642 2.05844 2.05844 1.76642 2.42597 1.61418C2.70164 1.5 3.05109 1.5 3.75 1.5H6.75C7.44891 1.5 7.79835 1.5 8.07405 1.61418C8.44155 1.76642 8.7336 2.05844 8.88585 2.42597C9 2.70164 9 3.05109 9 3.75C9 4.44891 9 4.79836 8.88585 5.07403C8.7336 5.44156 8.44155 5.73358 8.07405 5.88582C7.79835 6 7.44891 6 6.75 6H3.75C3.05109 6 2.70164 6 2.42597 5.88582C2.05844 5.73358 1.76642 5.44156 1.61418 5.07403C1.5 4.79836 1.5 4.44891 1.5 3.75Z"
                                    stroke="#FFFFFF" stroke-width="1.5" />
                            </svg>
                        </div>
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                </li>

                <!-- Affiliate-specific menu items -->
                <li>
                    <a href="{{ route('affiliate.wallet.index') }}"
                        class="d-flex align-items-center cg-10 {{ @$activeMyAffiliations }}">
                        <div class="d-flex">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                fill="none">
                                <path
                                    d="M3 6.75H15V13.5C15 14.3284 14.3284 15 13.5 15H4.5C3.67157 15 3 14.3284 3 13.5V6.75Z"
                                    stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M3 6.75L15 6.75L13.5 3H4.5L3 6.75Z" stroke="#FFFFFF" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M9 10.5H12" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                        <span>{{ __('My Wallet') }}</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('affiliate.students.index') }}"
                        class="d-flex align-items-center cg-10 {{ @$activeStudents }}">
                        <div class="d-flex">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                fill="none">
                                <path d="M9 9C11.2091 9 13 7.20914 13 5C13 2.79086 11.2091 1 9 1C6.79086 1 5 2.79086 5 5C5 7.20914 6.79086 9 9 9Z" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M3 17V15C3 12.7909 4.79086 11 7 11H11C13.2091 11 15 12.7909 15 15V17" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span>{{ __('My Students') }}</span>
                    </a>
                </li>

                {{-- <li>
                    <a href="{{ route('affiliate.become_affiliate') }}"
                        class="d-flex align-items-center cg-10 {{ @$activeBecomeAffiliate }}">
                        <div class="d-flex">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18"
                                fill="none">
                                <path d="M9 4.5V13.5" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M4.5 9H13.5" stroke="#FFFFFF" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </div>
                        <span>{{ __('Become Affiliate') }}</span>
                    </a>
                </li> --}}


                <li>
                    <a href="{{ route('affiliate.profile.index') }}"
                        class="d-flex align-items-center cg-10 {{ @$activeProfile }}">
                        <div class="d-flex">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                viewBox="0 0 20 20" fill="none">
                                <path
                                    d="M5.48131 12.9014C4.30234 13.6034 1.21114 15.0369 3.09389 16.8306C4.01359 17.7068 5.03791 18.3334 6.32573 18.3334H13.6743C14.9621 18.3334 15.9864 17.7068 16.9061 16.8306C18.7888 15.0369 15.6977 13.6034 14.5187 12.9014C11.754 11.2552 8.24599 11.2552 5.48131 12.9014Z"
                                    stroke="white" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path
                                    d="M13.75 5.41675C13.75 7.48781 12.0711 9.16675 10 9.16675C7.92893 9.16675 6.25 7.48781 6.25 5.41675C6.25 3.34568 7.92893 1.66675 10 1.66675C12.0711 1.66675 13.75 3.34568 13.75 5.41675Z"
                                    stroke="white" stroke-width="1.5" />
                            </svg>
                        </div>
                        <span class="">{{ __('Profile') }}</span>
                    </a>
                </li>
            </ul>
            <x-genz.sidebar-logout />
        </div>
    </div>
</div>
