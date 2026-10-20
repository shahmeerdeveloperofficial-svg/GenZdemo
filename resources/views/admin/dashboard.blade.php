@extends('layouts.app')

@push('title')
    {{ $pageTitle }}
@endpush

@push('style')
    <style>
        .genz-dashboard { --dash-navy:var(--genz-primary); --dash-cyan:var(--genz-cyan); }
        @keyframes genz-dashboard-enter { from { opacity:0; transform:translate3d(0,18px,0) scale(.99); } to { opacity:1; transform:translate3d(0,0,0) scale(1); } }
        .genz-dashboard__header { align-items:flex-start; animation:genz-dashboard-enter .58s cubic-bezier(.2,.75,.25,1) both; }
        .genz-dashboard__kpi-grid > [class*="col-"] { animation:genz-dashboard-enter .62s cubic-bezier(.2,.75,.25,1) both; animation-delay:var(--dashboard-delay,90ms); }
        .genz-dashboard__chart-card { animation:genz-dashboard-enter .64s cubic-bezier(.2,.75,.25,1) both; }
        .genz-dashboard > section:nth-of-type(2) .row > div:nth-child(1) .genz-dashboard__chart-card { animation-delay:690ms; }
        .genz-dashboard > section:nth-of-type(2) .row > div:nth-child(2) .genz-dashboard__chart-card { animation-delay:790ms; }
        .genz-dashboard > section:nth-of-type(2) .row > div:nth-child(3) .genz-dashboard__chart-card { animation-delay:890ms; }
        .genz-dashboard > section:nth-of-type(2) .row > div:nth-child(4) .genz-dashboard__chart-card { animation-delay:990ms; }
        .genz-dashboard__list-card { animation:genz-dashboard-enter .64s cubic-bezier(.2,.75,.25,1) both; animation-delay:1.05s; }
        .genz-dashboard > section:nth-of-type(3) .row > div:nth-child(2) .genz-dashboard__list-card { animation-delay:1.15s; }
        .genz-dashboard__eyebrow { color:var(--genz-muted); font-size:.92rem; letter-spacing:.02em; }
        .genz-dashboard__title { margin:0; font-size:clamp(1.65rem,2.4vw,2.15rem); font-weight:750; letter-spacing:-.035em; }
        .genz-dashboard__action-link { display:inline-flex; align-items:center; justify-content:center; gap:9px; min-height:42px; padding:9px 15px; border:1px solid var(--genz-border); border-radius:11px; background:var(--genz-surface); color:var(--dash-navy); font-size:.9rem; font-weight:700; text-decoration:none; transition:transform .18s ease,border-color .18s ease,box-shadow .18s ease,background-color .18s ease; }
        .genz-dashboard__action-link:hover { transform:translateY(-2px); border-color:var(--genz-cyan); background:var(--genz-bg); box-shadow:0 9px 20px rgb(7 53 121 / 9%); color:var(--dash-navy); }
        .genz-dashboard .home-card-item { position:relative; display:flex; width:100%; min-height:126px; height:100%; flex-direction:column; align-items:flex-start; padding:19px 20px; color:inherit; text-decoration:none; transition:transform .18s ease,box-shadow .18s ease,border-color .18s ease; }
        .genz-dashboard #dashboard-kpis-heading + .row > [class*="col-"] { display:flex; }
        .genz-dashboard a.home-card-item:hover,.genz-dashboard a.home-card-item:focus-visible { transform:translateY(-4px); border-color:var(--genz-cyan) !important; box-shadow:0 17px 34px rgb(7 53 121 / 13%); }
        .genz-dashboard__kpi-icon { position:absolute; top:17px; right:17px; display:grid; width:43px; height:43px; place-items:center; border:1px solid color-mix(in srgb,var(--kpi-accent) 20%,transparent); border-radius:14px; background:color-mix(in srgb,var(--kpi-accent) 12%,var(--genz-surface)); color:var(--kpi-accent); font-size:17px; transition:transform .22s ease,box-shadow .22s ease; }
        .genz-dashboard a.home-card-item:hover .genz-dashboard__kpi-icon { transform:rotate(-7deg) scale(1.08); box-shadow:0 7px 18px color-mix(in srgb,var(--kpi-accent) 22%,transparent); }
        .genz-dashboard .home-card-item .no { min-height:2.1rem; margin:0; color:var(--dash-navy) !important; font-size:1.9rem; font-weight:800; letter-spacing:-.04em; line-height:1.1; }
        .genz-dashboard .home-card-item .info { display:flex; min-height:40px; flex:1; align-items:flex-end; margin:12px 0 0; padding-right:35px; color:var(--genz-muted) !important; font-size:.92rem; line-height:1.35; }
        .genz-dashboard__card-arrow { position:absolute; right:17px; bottom:17px; color:#14a9d8; font-size:1rem; opacity:0; transform:translateX(-4px); transition:opacity .18s ease,transform .18s ease; }
        .genz-dashboard a.home-card-item:hover .genz-dashboard__card-arrow,.genz-dashboard a.home-card-item:focus-visible .genz-dashboard__card-arrow { opacity:1; transform:translateX(0); }
        .genz-dashboard__section-title { display:flex; align-items:center; gap:10px; margin:0; color:var(--genz-text); font-size:1.03rem; font-weight:750; }
        .genz-dashboard__section-icon { display:inline-grid; width:30px; height:30px; flex:0 0 30px; place-items:center; border-radius:9px; background:rgb(22 188 243 / 13%); color:var(--genz-primary); font-size:.82rem; }
        .genz-dashboard__section-note { color:var(--genz-muted); font-size:.82rem; }
        .genz-dashboard__chart-card,.genz-dashboard__list-card { height:100%; padding:21px; border:1px solid var(--genz-border); border-radius:15px; background:var(--genz-surface); box-shadow:var(--genz-shadow); transition:border-color .18s ease,box-shadow .18s ease; }
        .genz-dashboard__chart-card:hover { border-color:color-mix(in srgb,var(--genz-cyan) 45%,var(--genz-border)); box-shadow:0 13px 34px rgb(7 53 121 / 9%); }
        .genz-dashboard__chart { min-height:270px; }
        .genz-dashboard__chart--compact { min-height:250px; }
        .genz-dashboard__empty { margin:8px 0 0; color:var(--genz-muted); font-size:.86rem; }
        .genz-dashboard__list { display:grid; gap:0; }
        .genz-dashboard__list-item { display:flex; align-items:center; justify-content:space-between; gap:14px; padding:13px 0; border-bottom:1px solid var(--genz-border); color:inherit; text-decoration:none; }
        .genz-dashboard__list-item:last-child { border-bottom:0; }
        .genz-dashboard__avatar { display:grid; width:38px; height:38px; flex:0 0 38px; place-items:center; border:1px solid var(--genz-border); border-radius:12px; background:rgb(22 188 243 / 11%); color:var(--genz-primary); font-size:.83rem; font-weight:800; }
        .genz-dashboard__person-name { display:block; color:var(--genz-text); font-size:.9rem; font-weight:700; }
        .genz-dashboard__person-meta { display:block; max-width:270px; overflow:hidden; color:var(--genz-muted); font-size:.78rem; text-overflow:ellipsis; white-space:nowrap; }
        .genz-dashboard__badge { padding:5px 9px; border:1px solid var(--genz-border); border-radius:99px; background:rgb(22 188 243 / 10%); color:var(--genz-primary); font-size:.72rem; font-weight:750; text-transform:capitalize; }
        @media (max-width:767.98px) { .genz-dashboard__header { display:block; } .genz-dashboard__chart-card,.genz-dashboard__list-card { padding:16px; } .genz-dashboard .home-card-item { min-height:112px; padding:15px; } }
        @media (prefers-reduced-motion:reduce) { .genz-dashboard *, .genz-dashboard *::before, .genz-dashboard *::after { scroll-behavior:auto !important; transition:none !important; animation:none !important; } }
    </style>
@endpush

@section('content')
    <main class="genz-dashboard p-sm-30 p-15" aria-labelledby="admin-dashboard-heading">
        <header class="genz-dashboard__header mb-4">
            <div>
                <p class="genz-dashboard__eyebrow mb-1">{{ __('GenZ Consultants workspace') }}</p>
                <h1 id="admin-dashboard-heading" class="genz-dashboard__title">{{ __('Operations overview') }}</h1>
                <p class="text-muted mb-0 mt-2">{{ __('Live figures are calculated from records in this database. Demo records are fictional and stored separately.') }}</p>
            </div>
        </header>

        @php
            $dashboardCardValues = [
                'totalStudent' => $totalStudent, 'totalCourse' => $totalCourse,
                'totalEnrollments' => $totalEnrollments, 'newLeads' => $newLeads,
                'totalAdmissions' => $totalAdmissions, 'dueFollowUps' => $dueFollowUps,
                'pendingEnquiries' => $pendingEnquiries, 'totalAppointment' => $totalAppointment,
                'totalUniversity' => $totalUniversity, 'totalEvent' => $totalEvent,
                'totalService' => $totalService, 'totalStaff' => $totalStaff,
                'totalAffiliate' => $totalAffiliate, 'pendingInvoices' => $pendingInvoices,
            ];
            $dashboardCardIcons = [
                'totalStudent' => 'fa-user-graduate', 'totalCourse' => 'fa-book-open',
                'totalEnrollments' => 'fa-layer-group', 'newLeads' => 'fa-user-plus',
                'totalAdmissions' => 'fa-file-circle-check', 'dueFollowUps' => 'fa-list-check',
                'pendingEnquiries' => 'fa-comments', 'totalAppointment' => 'fa-calendar-check',
                'totalUniversity' => 'fa-building-columns', 'totalEvent' => 'fa-calendar-days',
                'totalService' => 'fa-briefcase-medical', 'totalStaff' => 'fa-users',
                'totalAffiliate' => 'fa-handshake', 'pendingInvoices' => 'fa-file-invoice-dollar',
            ];
            $dashboardCardAccents = [
                'totalStudent' => '#3d83e8', 'totalCourse' => '#7562d9',
                'totalEnrollments' => '#21a780', 'newLeads' => '#df9a24',
                'totalAdmissions' => '#1687b6', 'dueFollowUps' => '#d75d7a',
                'pendingEnquiries' => '#bb63a7', 'totalAppointment' => '#2398b6',
                'totalUniversity' => '#4e75c9', 'totalEvent' => '#dc8530',
                'totalService' => '#278b87', 'totalStaff' => '#4d80bd',
                'totalAffiliate' => '#9663c4', 'pendingInvoices' => '#d96c49',
            ];
            $dashboardCards = [
                ['key' => 'totalStudent', 'label' => __('Total students'), 'permission' => 'Manage Students', 'route' => 'admin.students.index'],
                ['key' => 'totalCourse', 'label' => __('Active courses'), 'permission' => 'Manage Courses', 'route' => 'admin.courses.index'],
                ['key' => 'totalEnrollments', 'label' => __('Active LMS enrollments'), 'permission' => 'Manage Courses', 'route' => 'admin.courses.index'],
                ['key' => 'newLeads', 'label' => __('New CRM leads'), 'permission' => 'Manage Leads', 'route' => 'admin.crm.leads.index'],
                ['key' => 'totalAdmissions', 'label' => __('Admissions'), 'permission' => 'Manage Admissions', 'route' => 'admin.admissions.index'],
                ['key' => 'dueFollowUps', 'label' => __('Due follow-ups'), 'permission' => 'Manage Leads', 'route' => 'admin.crm.leads.index'],
                ['key' => 'pendingEnquiries', 'label' => __('Pending consultation requests'), 'permission' => 'Manage Free Consultations', 'route' => 'admin.free_consultations.index'],
                ['key' => 'totalAppointment', 'label' => __('Appointments'), 'permission' => 'Manage Consultations', 'route' => 'admin.consultations.appointments.index'],
                ['key' => 'totalUniversity', 'label' => __('Published universities'), 'permission' => 'Manage Cms Settings', 'route' => 'admin.cms-settings.universities.index'],
                ['key' => 'totalEvent', 'label' => __('Active events'), 'permission' => 'Manage Events', 'route' => 'admin.events.index'],
                ['key' => 'totalService', 'label' => __('Active services'), 'permission' => 'Manage Service', 'route' => 'admin.services.index'],
                ['key' => 'totalStaff', 'label' => __('Active staff'), 'permission' => 'Manage Staffs', 'route' => null],
                ['key' => 'totalAffiliate', 'label' => __('Active affiliates'), 'permission' => 'Manage Affiliates', 'route' => null],
                ['key' => 'pendingInvoices', 'label' => __('Unpaid service invoices'), 'permission' => 'Manage Service', 'route' => 'admin.service_orders'],
            ];
        @endphp

        <section class="pb-md-35 pb-25" aria-labelledby="dashboard-kpis-heading">
            <h2 id="dashboard-kpis-heading" class="visually-hidden">{{ __('Key totals') }}</h2>
            <div class="row rg-20 genz-dashboard__kpi-grid">
                @foreach ($dashboardCards as $card)
                    @can($card['permission'])
                        <div class="col-xl-3 col-md-4 col-sm-6" style="--dashboard-delay:{{ 90 + ($loop->index * 42) }}ms">
                            @if ($card['route'])
                                <a class="home-card-item genz-card" href="{{ route($card['route']) }}" aria-label="{{ $card['label'] }}: {{ $dashboardCardValues[$card['key']] ?? 0 }}">
                                    <span class="genz-dashboard__kpi-icon" style="--kpi-accent:{{ $dashboardCardAccents[$card['key']] ?? '#1687b6' }}" aria-hidden="true"><i class="fa-solid {{ $dashboardCardIcons[$card['key']] ?? 'fa-chart-simple' }}"></i></span>
                                    <h3 class="no" data-dashboard-kpi="{{ $card['key'] }}">{{ $dashboardCardValues[$card['key']] ?? 0 }}</h3>
                                    <p class="info">{{ $card['label'] }}</p><span class="genz-dashboard__card-arrow" aria-hidden="true">↗</span>
                                </a>
                            @else
                                <div class="home-card-item genz-card">
                                    <span class="genz-dashboard__kpi-icon" style="--kpi-accent:{{ $dashboardCardAccents[$card['key']] ?? '#1687b6' }}" aria-hidden="true"><i class="fa-solid {{ $dashboardCardIcons[$card['key']] ?? 'fa-chart-simple' }}"></i></span>
                                    <h3 class="no" data-dashboard-kpi="{{ $card['key'] }}">{{ $dashboardCardValues[$card['key']] ?? 0 }}</h3>
                                    <p class="info">{{ $card['label'] }}</p>
                                </div>
                            @endif
                        </div>
                    @endcan
                @endforeach
            </div>
        </section>

        <section class="pb-md-35 pb-25" aria-labelledby="dashboard-charts-heading">
            <div class="d-flex align-items-end justify-content-between mb-3">
                <div>
                    <h2 id="dashboard-charts-heading" class="genz-dashboard__section-title"><span class="genz-dashboard__section-icon" aria-hidden="true"><i class="fa-solid fa-chart-line"></i></span>{{ __('Live activity') }}</h2>
                    <span class="genz-dashboard__section-note">{{ __('Charts update from saved enrollments, leads and applications.') }}</span>
                </div>
                <span class="genz-dashboard__section-note">{{ __('Auto-refreshes every minute') }}</span>
            </div>
            <div class="row rg-20">
                <div class="col-xl-6">
                    <article class="genz-dashboard__chart-card">
                        <h3 class="genz-dashboard__section-title"><span class="genz-dashboard__section-icon" aria-hidden="true"><i class="fa-solid fa-user-graduate"></i></span>{{ __('Student enrollment trend') }}</h3>
                        <div id="dashboard-enrollment-chart" class="genz-dashboard__chart" role="img" aria-label="{{ __('Monthly student enrollment trend') }}"></div>
                        <p class="genz-dashboard__empty {{ array_sum($enrollmentTrend['values']->all()) > 0 ? 'd-none' : '' }}" data-empty-for="enrollment">{{ __('Enrollments will appear here as students join courses.') }}</p>
                    </article>
                </div>
                <div class="col-xl-6">
                    <article class="genz-dashboard__chart-card">
                        <h3 class="genz-dashboard__section-title"><span class="genz-dashboard__section-icon" aria-hidden="true"><i class="fa-solid fa-filter-circle-dollar"></i></span>{{ __('CRM lead pipeline') }}</h3>
                        <div id="dashboard-lead-chart" class="genz-dashboard__chart" role="img" aria-label="{{ __('Leads grouped by pipeline status') }}"></div>
                        <p class="genz-dashboard__empty {{ array_sum($leadPipeline['values']->all()) > 0 ? 'd-none' : '' }}" data-empty-for="leads">{{ __('Lead stages will populate when enquiries are recorded.') }}</p>
                    </article>
                </div>
                <div class="col-xl-6">
                    <article class="genz-dashboard__chart-card">
                        <h3 class="genz-dashboard__section-title"><span class="genz-dashboard__section-icon" aria-hidden="true"><i class="fa-solid fa-passport"></i></span>{{ __('Admissions by stage') }}</h3>
                        <div id="dashboard-admissions-chart" class="genz-dashboard__chart genz-dashboard__chart--compact" role="img" aria-label="{{ __('Applications grouped by admissions stage') }}"></div>
                        <p class="genz-dashboard__empty {{ array_sum($admissionsPipeline['values']->all()) > 0 ? 'd-none' : '' }}" data-empty-for="admissions">{{ __('Application stages will appear here when applications are created.') }}</p>
                    </article>
                </div>
                <div class="col-xl-6">
                    <article class="genz-dashboard__chart-card">
                        <h3 class="genz-dashboard__section-title"><span class="genz-dashboard__section-icon" aria-hidden="true"><i class="fa-solid fa-book-open"></i></span>{{ __('Most popular courses') }}</h3>
                        <div id="dashboard-courses-chart" class="genz-dashboard__chart genz-dashboard__chart--compact" role="img" aria-label="{{ __('Courses ranked by active enrollments') }}"></div>
                        <p class="genz-dashboard__empty {{ array_sum($coursePopularity['values']->all()) > 0 ? 'd-none' : '' }}" data-empty-for="courses">{{ __('Course rankings appear after students enroll.') }}</p>
                    </article>
                </div>
            </div>
        </section>

        <section class="pb-20" aria-label="{{ __('Recent records') }}">
            <div class="row rg-20">
                @can('Manage Students')
                    <div class="col-xl-6">
                        <article class="genz-dashboard__list-card">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div><h2 class="genz-dashboard__section-title">{{ __('Student directory') }}</h2><span class="genz-dashboard__section-note">{{ __('Recently added learners') }}</span></div>
                                <a class="genz-dashboard__action-link" href="{{ route('admin.students.index') }}">{{ __('View directory') }} <span aria-hidden="true">→</span></a>
                            </div>
                            <div class="genz-dashboard__list">
                                @forelse ($recentStudents as $student)
                                    <a class="genz-dashboard__list-item" href="{{ route('admin.students.index') }}">
                                        <span class="d-flex align-items-center gap-3"><span class="genz-dashboard__avatar">{{ mb_strtoupper(mb_substr($student->first_name, 0, 1)) }}</span><span><span class="genz-dashboard__person-name">{{ $student->name }}</span><span class="genz-dashboard__person-meta">{{ $student->email }}</span></span></span>
                                        <span class="genz-dashboard__badge">{{ $student->status == STATUS_ACTIVE ? __('Active') : __('Inactive') }}</span>
                                    </a>
                                @empty
                                    <p class="genz-dashboard__empty mb-0">{{ __('No students have been added yet.') }}</p>
                                @endforelse
                            </div>
                        </article>
                    </div>
                @endcan
                @can('Manage Leads')
                    <div class="col-xl-6">
                        <article class="genz-dashboard__list-card">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div><h2 class="genz-dashboard__section-title">{{ __('Recent CRM leads') }}</h2><span class="genz-dashboard__section-note">{{ __('Only leads visible to your account') }}</span></div>
                                <a class="genz-dashboard__action-link" href="{{ route('admin.crm.leads.index') }}">{{ __('View leads') }} <span aria-hidden="true">→</span></a>
                            </div>
                            <div class="genz-dashboard__list">
                                @forelse ($recentLeads as $lead)
                                    <a class="genz-dashboard__list-item" href="{{ route('admin.crm.leads.show', $lead) }}">
                                        <span class="d-flex align-items-center gap-3"><span class="genz-dashboard__avatar">{{ mb_strtoupper(mb_substr($lead->first_name, 0, 1)) }}</span><span><span class="genz-dashboard__person-name">{{ trim($lead->first_name.' '.$lead->last_name) }}</span><span class="genz-dashboard__person-meta">{{ $lead->email ?: $lead->source }}</span></span></span>
                                        <span class="genz-dashboard__badge">{{ __(ucfirst($lead->status)) }}</span>
                                    </a>
                                @empty
                                    <p class="genz-dashboard__empty mb-0">{{ __('No leads are visible yet.') }}</p>
                                @endforelse
                            </div>
                        </article>
                    </div>
                @endcan
            </div>
        </section>
    </main>
@endsection

@push('script')
    <script>
        (function () {
            const palette = ['#073579', '#16bcf3', '#4c8ef7', '#48c6a5', '#f2b84b', '#f27b72', '#8672ed'];
            const liveUrl = @json(route('admin.dashboard.metrics'));
            const charts = {};
            const chartTheme = document.documentElement.getAttribute('data-genz-theme') === 'dark' ? 'dark' : 'light';
            const themeTokens = getComputedStyle(document.body);
            const chartText = themeTokens.getPropertyValue('--genz-muted').trim() || '#60738e';
            const chartBorder = themeTokens.getPropertyValue('--genz-border').trim() || '#e9eff6';
            const chartSurface = themeTokens.getPropertyValue('--genz-surface').trim() || '#fff';
            const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            const chartMotion = () => ({
                enabled: !prefersReducedMotion,
                easing: 'easeinout',
                speed: 760,
                animateGradually: { enabled: true, delay: 95 },
                dynamicAnimation: { enabled: true, speed: 360 }
            });
            const initial = {
                enrollmentTrend: {{ Illuminate\Support\Js::from($enrollmentTrend) }},
                leadPipeline: {{ Illuminate\Support\Js::from($leadPipeline) }},
                admissionsPipeline: {{ Illuminate\Support\Js::from($admissionsPipeline) }},
                coursePopularity: {{ Illuminate\Support\Js::from($coursePopularity) }}
            };

            if (!prefersReducedMotion) {
                const numberFormatter = new Intl.NumberFormat();
                document.querySelectorAll('[data-dashboard-kpi]').forEach(node => {
                    const target = Number((node.textContent || '').replace(/[^\d.-]/g, ''));
                    if (!Number.isFinite(target) || target <= 0) return;

                    const startedAt = performance.now();
                    const duration = 820;
                    node.textContent = '0';
                    const countUp = now => {
                        const progress = Math.min((now - startedAt) / duration, 1);
                        const easedProgress = 1 - Math.pow(1 - progress, 3);
                        node.textContent = numberFormatter.format(Math.round(target * easedProgress));
                        if (progress < 1) requestAnimationFrame(countUp);
                    };
                    requestAnimationFrame(countUp);
                });
            }

            if (typeof ApexCharts === 'undefined') return;

            charts.enrollment = new ApexCharts(document.querySelector('#dashboard-enrollment-chart'), {
                chart: { id: 'dashboard-enrollment', type: 'area', height: 270, foreColor: chartText, toolbar: { show: false }, animations: chartMotion() },
                theme: { mode: chartTheme },
                series: [{ name: @json(__('Enrollments')), data: initial.enrollmentTrend.values }],
                xaxis: { categories: initial.enrollmentTrend.labels, labels: { rotate: -30 } },
                yaxis: { min: 0, forceNiceScale: true, decimalsInFloat: 0 },
                colors: [palette[1]], stroke: { curve: 'smooth', width: 3 },
                fill: { type: 'gradient', gradient: { opacityFrom: .32, opacityTo: .03 } },
                dataLabels: { enabled: false }, grid: { borderColor: chartBorder },
                tooltip: { y: { formatter: value => `${value} ${@json(__('students'))}` } }
            });

            charts.leads = new ApexCharts(document.querySelector('#dashboard-lead-chart'), {
                chart: { id: 'dashboard-leads', type: 'donut', height: 270, foreColor: chartText, animations: chartMotion() },
                theme: { mode: chartTheme },
                series: initial.leadPipeline.values, labels: initial.leadPipeline.labels,
                colors: palette, legend: { position: 'bottom', labels: { colors: chartText } }, stroke: { width: 3, colors: [chartSurface] },
                dataLabels: { enabled: true }, plotOptions: { pie: { donut: { size: '62%' } } },
                tooltip: { y: { formatter: value => `${value} ${@json(__('leads'))}` } }
            });

            charts.admissions = new ApexCharts(document.querySelector('#dashboard-admissions-chart'), {
                chart: { id: 'dashboard-admissions', type: 'bar', height: 330, foreColor: chartText, toolbar: { show: false }, animations: chartMotion() },
                theme: { mode: chartTheme },
                series: [{ name: @json(__('Applications')), data: initial.admissionsPipeline.values }],
                xaxis: { categories: initial.admissionsPipeline.labels, labels: { rotate: -35, trim: true } },
                colors: [palette[0]], plotOptions: { bar: { borderRadius: 5, columnWidth: '48%' } },
                dataLabels: { enabled: false }, grid: { borderColor: chartBorder },
                yaxis: { min: 0, forceNiceScale: true, decimalsInFloat: 0 }
            });

            charts.courses = new ApexCharts(document.querySelector('#dashboard-courses-chart'), {
                chart: { id: 'dashboard-courses', type: 'bar', height: 280, foreColor: chartText, toolbar: { show: false }, animations: chartMotion() },
                theme: { mode: chartTheme },
                series: [{ name: @json(__('Active enrollments')), data: initial.coursePopularity.values }],
                xaxis: { categories: initial.coursePopularity.labels, labels: { trim: true } },
                colors: [palette[1]], plotOptions: { bar: { horizontal: true, borderRadius: 5, barHeight: '58%' } },
                dataLabels: { enabled: true }, grid: { borderColor: chartBorder },
                yaxis: { labels: { maxWidth: 180 } }
            });

            Object.values(charts).forEach(chart => chart.render());

            document.addEventListener('genz:theme-change', event => {
                const nextTheme = event.detail.theme === 'dark' ? 'dark' : 'light';
                const tokens = getComputedStyle(document.body);
                const textColor = tokens.getPropertyValue('--genz-muted').trim() || '#60738e';
                const borderColor = tokens.getPropertyValue('--genz-border').trim() || '#e9eff6';
                const surfaceColor = tokens.getPropertyValue('--genz-surface').trim() || '#fff';
                Object.values(charts).forEach(chart => chart.updateOptions({
                    theme: { mode: nextTheme },
                    chart: { foreColor: textColor },
                    grid: { borderColor: borderColor },
                    legend: { labels: { colors: textColor } },
                    xaxis: { labels: { style: { colors: textColor } }, axisBorder: { color: borderColor }, axisTicks: { color: borderColor } },
                    yaxis: { labels: { style: { colors: textColor } } },
                    stroke: { colors: [surfaceColor] }
                }, false, false, false));
            });

            function showEmptyState(key, values) {
                const note = document.querySelector(`[data-empty-for="${key}"]`);
                if (note) note.classList.toggle('d-none', values.some(value => Number(value) > 0));
            }

            function applyMetrics(data) {
                Object.entries(data.kpis || {}).forEach(([key, value]) => {
                    document.querySelectorAll(`[data-dashboard-kpi="${key}"]`).forEach(node => { node.textContent = value ?? '—'; });
                });
                charts.enrollment.updateOptions({ xaxis: { categories: data.enrollmentTrend.labels } });
                charts.enrollment.updateSeries([{ name: @json(__('Enrollments')), data: data.enrollmentTrend.values }]);
                charts.leads.updateOptions({ labels: data.leadPipeline.labels });
                charts.leads.updateSeries(data.leadPipeline.values);
                charts.admissions.updateOptions({ xaxis: { categories: data.admissionsPipeline.labels } });
                charts.admissions.updateSeries([{ name: @json(__('Applications')), data: data.admissionsPipeline.values }]);
                charts.courses.updateOptions({ xaxis: { categories: data.coursePopularity.labels } });
                charts.courses.updateSeries([{ name: @json(__('Active enrollments')), data: data.coursePopularity.values }]);
                showEmptyState('enrollment', data.enrollmentTrend.values);
                showEmptyState('leads', data.leadPipeline.values);
                showEmptyState('admissions', data.admissionsPipeline.values);
                showEmptyState('courses', data.coursePopularity.values);
            }

            async function refreshDashboard() {
                try {
                    const response = await fetch(liveUrl, { headers: { 'Accept': 'application/json' }, cache: 'no-store', credentials: 'same-origin' });
                    if (!response.ok) throw new Error('Dashboard refresh failed');
                    applyMetrics(await response.json());
                } catch (error) {
                    window.GenzToast?.error({
                        title: @json(__('Dashboard could not refresh')),
                        message: @json(__('Your saved records are unchanged. Check your connection and retry.')),
                        action: { label: @json(__('Retry')), onClick: refreshDashboard }
                    });
                }
            }

            window.setInterval(() => { if (!document.hidden) refreshDashboard(); }, 60000);
        })();
    </script>
@endpush
