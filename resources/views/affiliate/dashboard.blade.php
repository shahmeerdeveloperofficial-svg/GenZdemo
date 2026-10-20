@extends('layouts.app')
@push('title')
    {{ __('Dashboard') }}
@endpush


@section('content')
    <div class="p-sm-30 p-15">


        <!-- Affiliate Stats Section -->
        <div class="pb-md-40 pb-20">
            <div class="row rg-20">
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="home-card-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="no">{{ $totalAffiliateCount }}</h4>
                            <div class="icon"><img src="{{ asset('assets/images/icon/total-student.svg') }}"
                                    alt="" />
                            </div>
                        </div>
                        <p class="info">{{ __('Total Number of Affiliate') }}</p>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="home-card-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="no">{{ get_currency_symbol() . $totalAffiliate }}</h4>
                            <div class="icon"><img src="{{ asset('assets/images/icon/service-purchased.svg') }}"
                                    alt="" /></div>
                        </div>
                        <p class="info">{{ __('Total Affiliate') }}</p>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="home-card-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="no">{{ get_currency_symbol() . $totalCommission }}</h4>
                            <div class="icon"><img src="{{ asset('assets/images/icon/course-purchased.svg') }}" alt="" />
                            </div>
                        </div>
                        <p class="info">{{ __('Total Commission Earnings') }}</p>
                    </div>
                </div>
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="home-card-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="no">
                                {{ \App\Models\User::where('role', USER_ROLE_STUDENT)->where('parent_id', auth()->id())->count() }}
                            </h4>
                            <div class="icon"><img src="{{ asset('assets/images/icon/total-student.svg') }}"
                                    alt="" />
                            </div>
                        </div>
                        <p class="info">{{ __('Total Students Created') }}</p>
                    </div>
                </div>
                <div class="col-xl-4 col-md-6 col-sm-12">
                    <div class="home-card-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="mb-2">{{ __('Your Referral Link') }}</h5>
                                <p class="text-primary font-weight-bold mb-1" id="referralLink">
                                    {{ url('/') }}/?affiliate={{ $affiliateKey }}</p>
                                <p class="info mb-0">
                                    {{ __('Share this link with students to earn commissions when they make purchases') }}
                                </p>
                            </div>
                            <div class="icon">
                                <button class="btn btn-primary"
                                    onclick="copyToClipboard('{{ url('/') }}/?affiliate={{ $affiliateKey }}')">
                                    <i class="fa fa-copy"></i> {{ __('Copy Link') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>


        <!-- Commission Enabled Services -->
        <div class="pb-20">
            <div class="row rg-20">
                <!-- Courses -->
                <div class="col-md-4">
                    <div class="home-card-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="mb-2">{{ __('Courses') }}</h5>
                                <span
                                    class="badge {{ $commissionEnabled['course'] ? 'bg-success' : 'bg-danger' }}">
                                    {{ $commissionEnabled['course'] ? __('Enabled') : __('Disabled') }}
                                </span>
                                <p class="info mt-2 mb-0">
                                    <strong>{{ __('Rate') }}:
                                        {{ $commissionRates['course'] }}%</strong><br>
                                    <small>{{ __('Levels') }}: {{ $commissionLevels['course'] }}</small><br>
                                    {{ __('Earn commission on course purchases') }}
                                </p>
                            </div>
                            <div class="icon">
                                <i class="fa-solid fa-book {{ $commissionEnabled['course'] ? 'text-success' : 'text-danger' }}"
                                    style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Services -->
                <div class="col-md-4">
                    <div class="home-card-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="mb-2">{{ __('Services') }}</h5>
                                <span
                                    class="badge {{ $commissionEnabled['service'] ? 'bg-success' : 'bg-danger' }}">
                                    {{ $commissionEnabled['service'] ? __('Enabled') : __('Disabled') }}
                                </span>
                                <p class="info mt-2 mb-0">
                                    <strong>{{ __('Rate') }}:
                                        {{ $commissionRates['service'] }}%</strong><br>
                                    <small>{{ __('Levels') }}: {{ $commissionLevels['service'] }}</small><br>
                                    {{ __('Earn commission on service purchases') }}
                                </p>
                            </div>
                            <div class="icon">
                                <i class="fa-solid fa-tools {{ $commissionEnabled['service'] ? 'text-success' : 'text-danger' }}"
                                    style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Consultations -->
                <div class="col-md-4">
                    <div class="home-card-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <h5 class="mb-2">{{ __('Consultations') }}</h5>
                                <span
                                    class="badge {{ $commissionEnabled['consultation'] ? 'bg-success' : 'bg-danger' }}">
                                    {{ $commissionEnabled['consultation'] ? __('Enabled') : __('Disabled') }}
                                </span>
                                <p class="info mt-2 mb-0">
                                    <strong>{{ __('Rate') }}:
                                        {{ $commissionRates['consultation'] }}%</strong><br>
                                    <small>{{ __('Levels') }}:
                                        {{ $commissionLevels['consultation'] }}</small><br>
                                    {{ __('Earn commission on consultation bookings') }}
                                </p>
                            </div>
                            <div class="icon">
                                <i class="fa-solid fa-user-tie {{ $commissionEnabled['consultation'] ? 'text-success' : 'text-danger' }}"
                                    style="font-size: 1.5rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- Affiliate Charts Section -->
        <div class="pb-30">
            <div class="row rg-20">
                <div class="col-lg-6">
                    <div class="p-25 bd-one bd-c-stroke bd-ra-10 bg-white h-100">
                        <div>
                            <h4 class="fs-18 fw-500 lh-22 text-title-text">{{ __('Commission History') }}</h4>
                            <div id="affiliate-commission-chart"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="p-25 bd-one bd-c-stroke bd-ra-10 bg-white h-100">
                        <div>
                            <h4 class="fs-18 fw-500 lh-22 text-title-text">{{ __('Referral History') }}</h4>
                            <div id="affiliate-referral-chart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        {{-- <!-- Earning History Section (Custom Search Only Here) -->

        <div class="p-sm-25 p-15 bd-one bd-c-stroke bd-ra-10 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fs-18 lh-24 text-title-text mb-0">{{ __('Earning history') }}</h4>
                <div id="earningHistoryButtons" class="d-flex gap-2"></div>
            </div>
            <div class="tableFilter-wrap mb-3">
                <div class="item">
                    <label for="earning_search_key" class="zForm-label-alt">{{ __('Search') }}</label>
                    <input type="text" class="form-control zForm-control-alt" name="earning_search_key"
                        id="earning_search_key" placeholder="{{ __('Search By Item, Date') }}" />
                </div>
            </div>
            <div>
                <table id="affiliateDashboardTable" class="table zTable zTable-last-item-right">
                    <thead>
                        <tr>
                            <th>
                                <div>{{ __('Date') }}</div>
                            </th>
                            <th>
                                <div>{{ __('Title') }}</div>
                            </th>
                            <th>
                                <div>{{ __('Actual Amount') }}</div>
                            </th>
                            <th>
                                <div>{{ __('Earned Amount') }}</div>
                            </th>
                            <th>
                                <div>{{ __('Commission Percentage') }}</div>
                            </th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div> --}}


    </div>
    <input type="hidden" id="commission-month-category-list" value="{{ $commissionMonth }}">
    <input type="hidden" id="commission-chart-data" value="{{ $commissionChartData }}">
    <input type="hidden" id="referral-month-category-list" value="{{ $referralMonth }}">
    <input type="hidden" id="referral-chart-data" value="{{ $referralChartData }}">
@endsection

@push('style')
    <style>
        .home-card-item {
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            transition: all 0.3s ease;
        }

        .home-card-item:hover {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .border-success {
            border-color: #28a745 !important;
        }

        .border-danger {
            border-color: #dc3545 !important;
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }
    </style>
@endpush

@push('head')
    <!-- DataTables Buttons extension CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
@endpush

@push('script')
    <!-- ApexCharts JS -->
    <script src="{{ asset('assets/js/apexcharts.min.js') }}"></script>
    <!-- DataTables and Buttons extension JS -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script>
        window.affiliateRoutes = {
            myAffiliations: "{{ route('affiliate.my_affiliations') }}"
        };

        function copyToClipboard(text) {
            // Since site is HTTP, clipboard API won't work, use fallback
            var textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed"; // Avoid scrolling to bottom
            textArea.style.left = "-999999px";
            textArea.style.top = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                toastr.success('Referral link copied to clipboard!');
            } catch (err) {
                toastr.error('Failed to copy. Please copy manually: ' + text);
            }
            document.body.removeChild(textArea);
        }
    </script>
    <script src="{{ asset('admin/custom/js/affiliates-dashboard.js') }}"></script>
@endpush
