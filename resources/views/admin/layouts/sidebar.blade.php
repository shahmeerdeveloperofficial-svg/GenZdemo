<div class="zSidebar">
    <div class="zSidebar-overlay"></div>
    <div class="zSidebar-wrap h-100">
        <x-genz.logo :href="route('admin.dashboard')" :inverse="true" :compact="true" class="zSidebar-logo" />
        <x-genz.sidebar-toggle />

        <div class="zSidebar-fixed">
            <div class="px-20 pb-15">
                <label class="visually-hidden" for="genz-navigation-search">{{ __('Search navigation') }}</label>
                <input id="genz-navigation-search" type="search" class="form-control genz-field"
                    placeholder="{{ __('Search menu...') }}" autocomplete="off" />
            </div>

            @php
                $consultationsOpen = request()->routeIs('admin.consultations.*')
                    || (request()->routeIs('admin.orders.index') && request()->route('orderType') === 'consultation');
                $eventsOpen = request()->routeIs('admin.events.*')
                    || (request()->routeIs('admin.orders.index') && request()->route('orderType') === 'event');
                $coursesOpen = request()->routeIs('admin.courses.*')
                    || (request()->routeIs('admin.orders.index') && request()->route('orderType') === 'course');
                $catalogOpen = request()->routeIs(
                    'admin.study_levels.*',
                    'admin.cms-settings.countries.*',
                    'admin.cms-settings.universities.*',
                    'admin.cms-settings.academic-programs.*',
                    'admin.cms-settings.scholarships.*',
                    'admin.subjects.*'
                );
                $contentOpen = request()->routeIs(
                    'admin.cms-settings.section-settings',
                    'admin.cms-settings.public-site.*',
                    'admin.cms-settings.about-us.*',
                    'admin.cms-settings.blogs.*',
                    'admin.seo.*',
                    'admin.contact_us.*'
                );
            @endphp

            <ul class="zSidebar-menu" id="sidebarMenu">
                <li class="sidebar-section-label">{{ __('Workspace') }}</li>
                <li>
                    <a href="{{ route('admin.dashboard') }}"
                        class="d-flex align-items-center cg-10 {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                        @if (request()->routeIs('admin.dashboard')) aria-current="page" @endif>
                        <span aria-hidden="true"><i class="fa-solid fa-gauge-high"></i></span>
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                </li>

                @canany(['Manage Leads', 'Manage Free Consultations', 'Manage Consultations', 'Manage Students', 'Manage Admissions'])
                    <li class="sidebar-section-label">{{ __('CRM & Admissions') }}</li>
                    @can('Manage Leads')
                        <li>
                            <a href="{{ route('admin.crm.leads.index') }}"
                                class="d-flex align-items-center cg-10 {{ request()->routeIs('admin.crm.leads.*') ? 'active' : '' }}"
                                @if (request()->routeIs('admin.crm.leads.*')) aria-current="page" @endif>
                                <span aria-hidden="true"><i class="fa-solid fa-user-plus"></i></span>
                                <span>{{ __('Lead CRM') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('Manage Free Consultations')
                        <li>
                            <a href="{{ route('admin.free_consultations.index') }}"
                                class="d-flex align-items-center cg-10 {{ request()->routeIs('admin.free_consultations.*') ? 'active' : '' }}"
                                @if (request()->routeIs('admin.free_consultations.*')) aria-current="page" @endif>
                                <span aria-hidden="true"><i class="fa-regular fa-comments"></i></span>
                                <span>{{ __('Consultation Requests') }}</span>
                            </a>
                        </li>
                    @endcan

                    @can('Manage Consultations')
                        <li>
                            <a href="#adminNavConsultations"
                                class="d-flex align-items-center cg-10 {{ $consultationsOpen ? 'active' : '' }}"
                                data-bs-toggle="collapse" data-bs-target="#adminNavConsultations"
                                aria-expanded="{{ $consultationsOpen ? 'true' : 'false' }}"
                                aria-controls="adminNavConsultations">
                                <span aria-hidden="true"><i class="fa-regular fa-calendar-check"></i></span>
                                <span>{{ __('Consultations') }}</span>
                            </a>
                            <div id="adminNavConsultations" class="collapse {{ $consultationsOpen ? 'show' : '' }}"
                                data-bs-parent="#sidebarMenu">
                                <ul class="zSidebar-submenu">
                                    <li><a class="{{ request()->routeIs('admin.consultations.slots.*') ? 'active' : '' }}" href="{{ route('admin.consultations.slots.index') }}">{{ __('Availability slots') }}</a></li>
                                    <li><a class="{{ request()->routeIs('admin.consultations.meeting_platforms.*') ? 'active' : '' }}" href="{{ route('admin.consultations.meeting_platforms.index') }}">{{ __('Meeting platforms') }}</a></li>
                                    <li><a class="{{ request()->routeIs('admin.consultations.index') ? 'active' : '' }}" href="{{ route('admin.consultations.index') }}">{{ __('Consultants') }}</a></li>
                                    <li><a class="{{ request()->routeIs('admin.consultations.appointments.*') ? 'active' : '' }}" href="{{ route('admin.consultations.appointments.index') }}">{{ __('Appointments') }}</a></li>
                                    <li><a class="{{ request()->routeIs('admin.orders.index') && request()->route('orderType') === 'consultation' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['orderType' => 'consultation']) }}">{{ __('Consultation orders') }}</a></li>
                                    <li><a class="{{ request()->routeIs('admin.consultations.review.*') ? 'active' : '' }}" href="{{ route('admin.consultations.review.list') }}">{{ __('Reviews') }}</a></li>
                                </ul>
                            </div>
                        </li>
                    @endcan

                    @can('Manage Students')
                        <li>
                            <a href="{{ route('admin.students.index') }}"
                                class="d-flex align-items-center cg-10 {{ request()->routeIs('admin.students.*') ? 'active' : '' }}"
                                @if (request()->routeIs('admin.students.*')) aria-current="page" @endif>
                                <span aria-hidden="true"><i class="fa-solid fa-user-graduate"></i></span>
                                <span>{{ __('Student Directory') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.student_documents.index') }}"
                                class="d-flex align-items-center cg-10 {{ request()->routeIs('admin.student_documents.*') ? 'active' : '' }}"
                                @if (request()->routeIs('admin.student_documents.*')) aria-current="page" @endif>
                                <span aria-hidden="true"><i class="fa-regular fa-folder-open"></i></span>
                                <span>{{ __('Student Documents') }}</span>
                            </a>
                        </li>
                    @endcan

                @endcanany

                @can('Manage Cms Settings')
                    <li class="sidebar-section-label">{{ __('Study Abroad Setup') }}</li>
                    <li>
                        <a href="#adminNavCatalog"
                            class="d-flex align-items-center cg-10 {{ $catalogOpen ? 'active' : '' }}"
                            data-bs-toggle="collapse" data-bs-target="#adminNavCatalog"
                            aria-expanded="{{ $catalogOpen ? 'true' : 'false' }}" aria-controls="adminNavCatalog">
                            <span aria-hidden="true"><i class="fa-solid fa-earth-americas"></i></span>
                            <span>{{ __('Study Abroad Catalogue') }}</span>
                        </a>
                        <div id="adminNavCatalog" class="collapse {{ $catalogOpen ? 'show' : '' }}" data-bs-parent="#sidebarMenu">
                            <ul class="zSidebar-submenu">
                                <li><a class="{{ request()->routeIs('admin.cms-settings.countries.*') ? 'active' : '' }}" href="{{ route('admin.cms-settings.countries.index') }}">{{ __('Destinations') }}</a></li>
                                <li><a class="{{ request()->routeIs('admin.study_levels.*') ? 'active' : '' }}" href="{{ route('admin.study_levels.index') }}">{{ __('Study Levels') }}</a></li>
                                <li><a class="{{ request()->routeIs('admin.cms-settings.universities.*') ? 'active' : '' }}" href="{{ route('admin.cms-settings.universities.index') }}">{{ __('Universities') }}</a></li>
                                <li><a class="{{ request()->routeIs('admin.cms-settings.academic-programs.*') ? 'active' : '' }}" href="{{ route('admin.cms-settings.academic-programs.index') }}">{{ __('Academic Programs & Intakes') }}</a></li>
                                <li><a class="{{ request()->routeIs('admin.cms-settings.scholarships.*') ? 'active' : '' }}" href="{{ route('admin.cms-settings.scholarships.index') }}">{{ __('Scholarships') }}</a></li>
                                <li><a class="{{ request()->routeIs('admin.subjects.categories.*') ? 'active' : '' }}" href="{{ route('admin.subjects.categories.index') }}">{{ __('Subject Categories') }}</a></li>
                                <li><a class="{{ request()->routeIs('admin.subjects.index') ? 'active' : '' }}" href="{{ route('admin.subjects.index') }}">{{ __('Subjects') }}</a></li>
                            </ul>
                        </div>
                    </li>
                @endcan

                @can('Manage Admissions')
                    <li class="sidebar-section-label">{{ __('Applications') }}</li>
                    <li>
                        <a href="{{ route('admin.admissions.index') }}"
                            class="d-flex align-items-center cg-10 {{ request()->routeIs('admin.admissions.*') ? 'active' : '' }}"
                            @if (request()->routeIs('admin.admissions.*')) aria-current="page" @endif>
                            <span aria-hidden="true"><i class="fa-solid fa-passport"></i></span>
                            <span>{{ __('Admissions') }}</span>
                        </a>
                    </li>
                @endcan

                @can('Manage Courses')
                    <li class="sidebar-section-label">{{ __('Learning Management') }}</li>
                    <li>
                        <a href="#adminNavCourses" class="d-flex align-items-center cg-10 {{ $coursesOpen ? 'active' : '' }}"
                            data-bs-toggle="collapse" data-bs-target="#adminNavCourses"
                            aria-expanded="{{ $coursesOpen ? 'true' : 'false' }}" aria-controls="adminNavCourses">
                            <span aria-hidden="true"><i class="fa-solid fa-graduation-cap"></i></span>
                            <span>{{ __('Courses & Learning') }}</span>
                        </a>
                        <div id="adminNavCourses" class="collapse {{ $coursesOpen ? 'show' : '' }}" data-bs-parent="#sidebarMenu">
                            <ul class="zSidebar-submenu">
                                <li><a class="{{ request()->routeIs('admin.courses.programs.*') ? 'active' : '' }}" href="{{ route('admin.courses.programs.index') }}">{{ __('Course Tracks') }}</a></li>
                                <li><a class="{{ request()->routeIs('admin.courses.index', 'admin.courses.create', 'admin.courses.edit') ? 'active' : '' }}" href="{{ route('admin.courses.index') }}">{{ __('Courses') }}</a></li>
                                <li><a class="{{ request()->routeIs('admin.orders.index') && request()->route('orderType') === 'course' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['orderType' => 'course']) }}">{{ __('Course Orders') }}</a></li>
                            </ul>
                        </div>
                    </li>
                @endcan

                @canany(['Manage Service', 'Manage Events'])
                    <li class="sidebar-section-label">{{ __('Services & Activities') }}</li>
                    @can('Manage Service')
                        <li>
                            <a href="{{ route('admin.services.index') }}" class="d-flex align-items-center cg-10 {{ request()->routeIs('admin.services.*') ? 'active' : '' }}">
                                <span aria-hidden="true"><i class="fa-solid fa-briefcase"></i></span>
                                <span>{{ __('Manage Services') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.service_orders') }}" class="d-flex align-items-center cg-10 {{ request()->routeIs('admin.service_orders*') ? 'active' : '' }}">
                                <span aria-hidden="true"><i class="fa-solid fa-list-check"></i></span>
                                <span>{{ __('Service Orders & Tasks') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.service_invoices.index') }}" class="d-flex align-items-center cg-10 {{ request()->routeIs('admin.service_invoices.*') ? 'active' : '' }}">
                                <span aria-hidden="true"><i class="fa-regular fa-file-lines"></i></span>
                                <span>{{ __('Service Invoices') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('Manage Events')
                        <li>
                            <a href="#adminNavEvents" class="d-flex align-items-center cg-10 {{ $eventsOpen ? 'active' : '' }}"
                                data-bs-toggle="collapse" data-bs-target="#adminNavEvents"
                                aria-expanded="{{ $eventsOpen ? 'true' : 'false' }}" aria-controls="adminNavEvents">
                                <span aria-hidden="true"><i class="fa-regular fa-calendar-days"></i></span>
                                <span>{{ __('Events') }}</span>
                            </a>
                            <div id="adminNavEvents" class="collapse {{ $eventsOpen ? 'show' : '' }}" data-bs-parent="#sidebarMenu">
                                <ul class="zSidebar-submenu">
                                    <li><a class="{{ request()->routeIs('admin.events.index', 'admin.events.create', 'admin.events.edit') ? 'active' : '' }}" href="{{ route('admin.events.index') }}">{{ __('Manage Events') }}</a></li>
                                    <li><a class="{{ request()->routeIs('admin.orders.index') && request()->route('orderType') === 'event' ? 'active' : '' }}" href="{{ route('admin.orders.index', ['orderType' => 'event']) }}">{{ __('Event Orders') }}</a></li>
                                </ul>
                            </div>
                        </li>
                    @endcan
                @endcanany

                @canany(['Manage Transaction', 'Manage Affiliates'])
                    <li class="sidebar-section-label">{{ __('Finance & Referrals') }}</li>
                    @can('Manage Transaction')
                        <li>
                            <a href="{{ route('admin.transactions') }}" class="d-flex align-items-center cg-10 {{ request()->routeIs('admin.transactions*') ? 'active' : '' }}">
                                <span aria-hidden="true"><i class="fa-solid fa-money-bill-transfer"></i></span>
                                <span>{{ __('Transactions') }}</span>
                            </a>
                        </li>
                    @endcan
                    @can('Manage Affiliates')
                        <li>
                            <a href="#adminNavAffiliates" class="d-flex align-items-center cg-10 {{ request()->routeIs('admin.affiliates.*', 'admin.payouts.*') ? 'active' : '' }}"
                                data-bs-toggle="collapse" data-bs-target="#adminNavAffiliates"
                                aria-expanded="{{ request()->routeIs('admin.affiliates.*', 'admin.payouts.*') ? 'true' : 'false' }}"
                                aria-controls="adminNavAffiliates">
                                <span aria-hidden="true"><i class="fa-solid fa-users"></i></span>
                                <span>{{ __('Affiliate Program') }}</span>
                            </a>
                            <div id="adminNavAffiliates" class="collapse {{ request()->routeIs('admin.affiliates.*', 'admin.payouts.*') ? 'show' : '' }}" data-bs-parent="#sidebarMenu">
                                <ul class="zSidebar-submenu">
                                    <li><a class="{{ request()->routeIs('admin.affiliates.*') ? 'active' : '' }}" href="{{ route('admin.affiliates.index') }}">{{ __('Affiliates') }}</a></li>
                                    <li><a class="{{ request()->routeIs('admin.payouts.*') ? 'active' : '' }}" href="{{ route('admin.payouts.index') }}">{{ __('Affiliate Payouts') }}</a></li>
                                </ul>
                            </div>
                        </li>
                    @endcan
                @endcanany

                @canany(['Manage Courses', 'Manage Students', 'Manage Free Consultations', 'Manage Transaction'])
                    <li class="sidebar-section-label">{{ __('Insights') }}</li>
                    <li>
                        <a href="{{ route('admin.analytics') }}" class="d-flex align-items-center cg-10 {{ request()->routeIs('admin.analytics') ? 'active' : '' }}"
                            @if (request()->routeIs('admin.analytics')) aria-current="page" @endif>
                            <span aria-hidden="true"><i class="fa-solid fa-chart-line"></i></span>
                            <span>{{ __('Analytics & Reports') }}</span>
                        </a>
                    </li>
                @endcanany

                @can('Manage Staffs')
                    <li class="sidebar-section-label">{{ __('Team & Access') }}</li>
                    @if ((int) auth()->user()->role === USER_ROLE_ADMIN)
                        <li>
                            <a href="#adminNavTeam" class="d-flex align-items-center cg-10 {{ request()->routeIs('admin.staffs.*') ? 'active' : '' }}"
                                data-bs-toggle="collapse" data-bs-target="#adminNavTeam"
                                aria-expanded="{{ request()->routeIs('admin.staffs.*') ? 'true' : 'false' }}" aria-controls="adminNavTeam">
                                <span aria-hidden="true"><i class="fa-solid fa-user-shield"></i></span>
                                <span>{{ __('Team & Permissions') }}</span>
                            </a>
                            <div id="adminNavTeam" class="collapse {{ request()->routeIs('admin.staffs.*') ? 'show' : '' }}" data-bs-parent="#sidebarMenu">
                                <ul class="zSidebar-submenu">
                                    <li><a class="{{ request()->routeIs('admin.staffs.index') ? 'active' : '' }}" href="{{ route('admin.staffs.index') }}">{{ __('Staff Directory') }}</a></li>
                                    <li><a class="{{ request()->routeIs('admin.staffs.roles.*') ? 'active' : '' }}" href="{{ route('admin.staffs.roles.index') }}">{{ __('Roles & Permissions') }}</a></li>
                                </ul>
                            </div>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('admin.staffs.index') }}" class="d-flex align-items-center cg-10 {{ request()->routeIs('admin.staffs.*') ? 'active' : '' }}">
                                <span aria-hidden="true"><i class="fa-solid fa-user-group"></i></span>
                                <span>{{ __('Staff Directory') }}</span>
                            </a>
                        </li>
                    @endif
                @endcan

                @can('Manage Cms Settings')
                    <li class="sidebar-section-label">{{ __('Website & Content') }}</li>
                    <li>
                        <a href="#adminNavWebsite" class="d-flex align-items-center cg-10 {{ $contentOpen ? 'active' : '' }}"
                            data-bs-toggle="collapse" data-bs-target="#adminNavWebsite"
                            aria-expanded="{{ $contentOpen ? 'true' : 'false' }}" aria-controls="adminNavWebsite">
                            <span aria-hidden="true"><i class="fa-solid fa-globe"></i></span>
                            <span>{{ __('Website Content & SEO') }}</span>
                        </a>
                        <div id="adminNavWebsite" class="collapse {{ $contentOpen ? 'show' : '' }}" data-bs-parent="#sidebarMenu">
                            <ul class="zSidebar-submenu">
                                <li><a class="{{ request()->routeIs('admin.cms-settings.public-site.*') ? 'active' : '' }}" href="{{ route('admin.cms-settings.public-site.edit') }}">{{ __('Public Website Editor') }}</a></li>
                                <li><a class="{{ request()->routeIs('admin.cms-settings.section-settings') ? 'active' : '' }}" href="{{ route('admin.cms-settings.section-settings') }}">{{ __('Landing Page') }}</a></li>
                                <li><a class="{{ request()->routeIs('admin.cms-settings.about-us.*') ? 'active' : '' }}" href="{{ route('admin.cms-settings.about-us.index') }}">{{ __('About Us') }}</a></li>
                                <li><a class="{{ request()->routeIs('admin.cms-settings.blogs.*') ? 'active' : '' }}" href="{{ route('admin.cms-settings.blogs.index') }}">{{ __('Blogs & News') }}</a></li>
                                <li><a class="{{ request()->routeIs('admin.seo.*') ? 'active' : '' }}" href="{{ route('admin.seo.index') }}">{{ __('SEO Settings') }}</a></li>
                                <li><a class="{{ request()->routeIs('admin.contact_us.*') ? 'active' : '' }}" href="{{ route('admin.contact_us.list') }}">{{ __('Website Enquiries') }}</a></li>
                            </ul>
                        </div>
                    </li>
                @endcan

                @can('Manage Setting')
                    <li class="sidebar-section-label">{{ __('System') }}</li>
                    <li>
                        <a href="{{ route('admin.setting.configuration-settings') }}" class="d-flex align-items-center cg-10 {{ request()->routeIs('admin.setting.configuration-settings*') ? 'active' : '' }}">
                            <span aria-hidden="true"><i class="fa-solid fa-sliders"></i></span>
                            <span>{{ __('Configurations') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('admin.setting.application-settings') }}" class="d-flex align-items-center cg-10 {{ request()->routeIs('admin.setting.application-settings*') ? 'active' : '' }}">
                            <span aria-hidden="true"><i class="fa-solid fa-gear"></i></span>
                            <span>{{ __('Application Settings') }}</span>
                        </a>
                    </li>
                    @if ((int) auth()->user()->role === USER_ROLE_ADMIN)
                        <li>
                            <a href="{{ route('admin.file-version-update') }}" class="d-flex align-items-center cg-10 {{ request()->routeIs('admin.file-version-update*') ? 'active' : '' }}">
                                <span aria-hidden="true"><i class="fa-solid fa-arrows-rotate"></i></span>
                                <span>{{ __('System Update') }}</span>
                            </a>
                        </li>
                    @endif
                @endcan

                <li class="sidebar-section-label">{{ __('My Account') }}</li>
                <li>
                    <a href="{{ route('admin.notification.all') }}" class="d-flex align-items-center cg-10 {{ request()->routeIs('admin.notification.*') ? 'active' : '' }}">
                        <span aria-hidden="true"><i class="fa-regular fa-bell"></i></span>
                        <span>{{ __('Notifications') }}</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.profile.index') }}" class="d-flex align-items-center cg-10 {{ request()->routeIs('admin.profile.*') ? 'active' : '' }}">
                        <span aria-hidden="true"><i class="fa-regular fa-user"></i></span>
                        <span>{{ __('Profile & Security') }}</span>
                    </a>
                </li>
            </ul>

            <x-genz.sidebar-logout />
            <script src="{{ asset('genz/js/sidebar-search.js') }}?v=2" defer></script>
        </div>
    </div>
</div>
