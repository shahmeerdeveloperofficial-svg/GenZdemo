@extends('layouts.affiliate.app')
@push('title')
    {{ $pageTitle }}
@endpush
@section('content')
    <div data-aos="fade-up" data-aos-duration="1000" class="p-sm-30 p-15">
        <div class="row rg-20">
            <div class="col-xl-12">
                <div class="bd-one bd-c-stroke bd-ra-8 bd-bl-ra-0 bd-br-ra-0 pt-25 px-sm-25 px-10 bg-white">
                    <div class="d-flex flex-wrap justify-content-between align-items-center pb-20">
                        <h4 class="fs-18 fw-700 lh-24 text-title-text">{{ $pageTitle }}</h4>
                        <button data-bs-toggle="modal" data-bs-target="#add-modal"
                            class="flipBtn sf-flipBtn-primary flex-shrink-0">
                            + {{ __('Create Student') }}
                        </button>
                    </div>
                    <div class="">
                        <table class="table zTable zTable-last-item-right pt-15" id="studentDataTable">
                            <thead>
                                <tr>
                                    <th>
                                        <div>{{ __('Image') }}</div>
                                    </th>
                                    <th>
                                        <div>{{ __('Name') }}</div>
                                    </th>
                                    <th>
                                        <div>{{ __('Email') }}</div>
                                    </th>
                                    <th>
                                        <div>{{ __('Phone') }}</div>
                                    </th>
                                    <th>
                                        <div>{{ __('Status') }}</div>
                                    </th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="add-modal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 bd-ra-4 p-sm-25 p-15">
                <div
                    class="bd-b-one bd-c-stroke pb-20 mb-20 d-flex align-items-center flex-wrap justify-content-between g-10">
                    <h2 class="fs-18 fw-600 lh-22 text-title-text">{{ __('Create Student') }}</h2>
                    <div class="mClose">
                        <button type="button"
                            class="bd-one bd-c-stroke rounded-circle w-25 h-25 bg-transparent text-para-text"
                            data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                </div>
                <form class="ajax reset" action="{{ route('affiliate.students.store') }}" method="post"
                    data-handler="commonResponseForModal">
                    @csrf
                    <div class="row rg-20 mb-20">
                        <div class="col-md-6">
                            <label for="first_name" class="zForm-label-alt">{{ __('First Name') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="first_name" id="first_name"
                                placeholder="{{ __('Type First Name') }}" class="form-control zForm-control-alt">
                        </div>

                        <div class="col-md-6">
                            <label for="last_name" class="zForm-label-alt">{{ __('Last Name') }}
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="last_name" id="last_name" placeholder="{{ __('Type Last Name') }}"
                                class="form-control zForm-control-alt">
                        </div>

                        <div class="col-md-6">
                            <label for="email" class="zForm-label-alt">{{ __('Email') }} <span
                                    class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" placeholder="{{ __('Email') }}"
                                class="form-control zForm-control-alt">
                        </div>

                        <div class="col-md-6">
                            <label for="mobile" class="zForm-label-alt">{{ __('Mobile') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="mobile" id="mobile" placeholder="{{ __('Mobile') }}"
                                class="form-control zForm-control-alt">
                        </div>

                        <div class="col-md-6">
                            <label for="password" class="zForm-label-alt">{{ __('Password') }} <span
                                    class="text-danger">*</span></label>
                            <input type="password" name="password" id="password" placeholder="{{ __('Password') }}"
                                class="form-control zForm-control-alt">
                        </div>

                        <div class="col-md-6">
                            <label for="status" class="zForm-label-alt">{{ __('Status') }} <span
                                    class="text-danger">*</span></label>
                            <select class="sf-select-without-search" id="eventType" name="status">
                                <option value="{{ STATUS_ACTIVE }}">{{ __('Active') }}</option>
                                <option value="{{ STATUS_DEACTIVATE }}">{{ __('Deactivate') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="bd-c-stroke-2 justify-content-between align-items-center text-end pt-15">
                        <button type="submit" class="flipBtn sf-flipBtn-primary flex-shrink-0">
                            <div class="d-none h-25 spinner-border w-25" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                            {{ __('Submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Status Change Modal -->
    <div class="modal fade" id="status-change-modal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 bd-ra-4 p-sm-25 p-15">

            </div>
        </div>
    </div>

    <input type="hidden" id="student-route" value="{{ route('affiliate.students.index') }}">
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";

            $('#studentDataTable').DataTable({
                pageLength: 10,
                ordering: true,
                serverSide: true,
                processing: true,
                searching: true,
                responsive: true,
                ajax: $('#student-route').val(),
                language: {
                    paginate: {
                        previous: "<i class='fa-solid fa-angles-left'></i>",
                        next: "<i class='fa-solid fa-angles-right'></i>",
                    },
                },
                dom: '<>tr<"tableBottom"<"row align-items-center"<"col-sm-6"<"tableInfo"i>><"col-sm-6"<"tablePagi"p>>>><"clear">',
                columns: [{
                        "data": "image",
                        "name": "image",
                        responsivePriority: 1
                    },
                    {
                        "data": "name",
                        "name": "name",
                        responsivePriority: 2
                    },
                    {
                        "data": "email",
                        "name": "email"
                    },
                    {
                        "data": "mobile",
                        "name": "mobile",
                        responsivePriority: 3
                    },
                    {
                        "data": "status",
                        "name": "status",
                        searchable: false
                    },
                ]
            });

        })(jQuery)
    </script>
@endpush
