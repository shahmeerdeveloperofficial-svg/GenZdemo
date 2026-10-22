@extends('layouts.app')
@push('title')
    {{ $pageTitle }}
@endpush
@section('content')
    <div class="px-sm-30 p-15 bd-b-one bd-c-stroke-2 d-flex justify-content-between align-items-center">
        <h4 class="fs-18 fw-700 lh-24 text-title-text">{{$pageTitle}}</h4>
        <div class="d-flex align-items-center g-10">
            <button type="button" class="flipBtn sf-flipBtn-primary" data-bs-toggle="modal" data-bs-target="#add-lead-modal">
                <span>{{__('Add Consultation Request')}}</span>
            </button>
            <button type="button" class="flipBtn sf-flipBtn-secondary" data-bs-toggle="modal" data-bs-target="#import-leads-modal">
                <span>{{__('Import Consultation Requests')}}</span>
            </button>
        </div>
    </div>
    <div class="p-sm-30 p-15">
        <div class="p-sm-25 p-15 bd-one bd-c-stroke bd-ra-10 bg-white">
            <div class="">
                <table class="table zTable zTable-last-item-right pt-15" id="freeConsultationDataTable">
                    <thead>
                    <tr>
                        <th>
                            <div>{{__('Sl')}}</div>
                        </th>
                        <th>
                            <div>{{__('Name')}}</div>
                        </th>
                        <th>
                            <div>{{__('Email')}}</div>
                        </th>
                        <th>
                            <div>{{__('Mobile')}}</div>
                        </th>
                        <th>
                            <div>{{__('Study Level')}}</div>
                        </th>
                        <th>
                            <div>{{__('Status')}}</div>
                        </th>
                        <th>
                            <div>{{__('Action')}}</div>
                        </th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- Page content area end -->

    <!-- Add Lead Modal section start -->
    <div class="modal fade" id="add-lead-modal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 bd-ra-4 p-sm-25 p-15">
                <div class="bd-b-one bd-c-stroke pb-20 mb-20 d-flex align-items-center flex-wrap justify-content-between g-10">
                    <h2 class="fs-18 fw-600 lh-22 text-title-text">{{ __('Add Consultation Request') }}</h2>
                    <div class="mClose">
                        <button type="button" class="bd-one bd-c-stroke rounded-circle w-25 h-25 bg-transparent text-para-text" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                </div>
                <form class="ajax reset" action="{{ route('admin.free_consultations.store') }}" method="post" data-handler="commonResponseForModal">
                    @csrf
                    @include('admin.free_consultations.form')
                    <div class="bd-c-stroke-2 justify-content-between align-items-center text-end pt-15">
                        <button type="submit" class="flipBtn sf-flipBtn-primary flex-shrink-0">{{__('Submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Lead Modal section end -->

    <!-- Import Leads Modal section start -->
    <div class="modal fade" id="import-leads-modal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 bd-ra-4 p-sm-25 p-15">
                <div class="bd-b-one bd-c-stroke pb-20 mb-20 d-flex align-items-center flex-wrap justify-content-between g-10">
                    <h2 class="fs-18 fw-600 lh-22 text-title-text">{{ __('Import Consultation Requests') }}</h2>
                    <div class="mClose">
                        <button type="button" class="bd-one bd-c-stroke rounded-circle w-25 h-25 bg-transparent text-para-text" data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                </div>
                <form class="ajax reset" action="{{ route('admin.free_consultations.import') }}" method="post" enctype="multipart/form-data" data-handler="importLeadsHandler">
                    @csrf
                    <div class="mb-20">
                        <div class="alert alert-info">
                            <p class="mb-10"><strong>{{ __('Instructions:') }}</strong> {{ __('Download demo file') }} → {{ __('Fill data following format') }} → {{ __('Upload completed file') }}</p>
                            <div class="mb-10">
                                <strong>{{ __('Required Fields:') }}</strong> First Name, Last Name, Email, Mobile, Study Level
                            </div>
                            <div class="mb-10">
                                <strong>{{ __('Study Level Options:') }}</strong>
                                <a href="{{ route('admin.free_consultations.download_study_levels') }}" class="text-primary text-decoration-underline ms-2" target="_blank" title="{{__('Download Study Levels List')}}">
                                    <i class="fa-solid fa-download me-1"></i>{{__('Download List')}}
                                </a>
                                <!-- <div class="mt-5 text-muted small">{{ implode(', ', \App\Models\StudyLevel::where('status', STATUS_ACTIVE)->pluck('name')->toArray()) }}</div> -->
                            </div>
                            <div class="mb-10">
                                <strong>{{ __('Consultation Type Options:') }}</strong> Physical, Virtual
                            </div>
                            <div class="mb-10">
                                <strong>{{ __('Education Fund Type Options:') }}</strong> Self, Parents, Seeking Scholarship, Bank Loan, Employer Scholarship, Other
                            </div>
                            <div class="mb-0">
                                <strong>{{ __('Preferred Study Destination:') }}</strong> Multiple countries allowed (comma-separated).
                                <a href="{{ route('admin.free_consultations.download_countries') }}" class="text-primary text-decoration-underline ms-2" target="_blank" title="{{__('Download Countries List')}}">
                                    <i class="fa-solid fa-download me-1"></i>{{__('Download List')}}
                                </a>
                                <!-- <div class="mt-5 text-muted small">{{ implode(', ', \App\Models\Country::where('status', STATUS_ACTIVE)->pluck('name')->toArray()) }}</div> -->
                            </div>
                        </div>
                        <div class="mb-20">
                            <a href="{{ route('admin.free_consultations.download_demo') }}" class="flipBtn sf-flipBtn-secondary">
                                <span><i class="fa-solid fa-download me-2"></i>{{__('Download Demo Excel File')}}</span>
                            </a>
                        </div>
                        <div class="mb-20">
                            <label for="import_file" class="zForm-label-alt">{{__('Upload Excel File')}} <span class="text-danger">*</span></label>
                            <input type="file" name="import_file" id="import_file" class="form-control zForm-control-alt" accept=".xlsx,.xls,.csv" required>
                            <small class="text-muted">{{__('Accepted formats: .xlsx, .xls, .csv (Note: Excel support requires PhpSpreadsheet package)')}}</small>
                        </div>
                    </div>
                    <div class="bd-c-stroke-2 justify-content-between align-items-center text-end pt-15">
                        <button type="submit" class="flipBtn sf-flipBtn-primary flex-shrink-0">{{__('Import')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Import Leads Modal section end -->

    <!-- Edit Modal section start -->
    <div class="modal fade" id="view-modal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 bd-ra-4 p-sm-25 p-15">

            </div>
        </div>
    </div>

    <input type="hidden" id="free-consultations-route" value="{{ route('admin.free_consultations.index') }}">

    <!-- Edit Modal section end -->
@endsection
@push('script')
    <script src="{{asset('admin/custom/js/free_consultations.js')}}"></script>
    <script>
        function importLeadsHandler(response) {
            if (response.status === true) {
                window.location.href = response.data.redirect_url;
            } else {
                toastr.error(response.message || '{{__("Something went wrong")}}');
            }
        }
    </script>
@endpush
