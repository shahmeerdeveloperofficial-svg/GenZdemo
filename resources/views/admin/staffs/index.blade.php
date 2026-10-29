@extends('layouts.app')
@push('title')
    {{ $pageTitle }}
@endpush
@section('content')
    <div class="px-sm-30 p-15 bd-b-one bd-c-stroke-2 d-flex justify-content-between align-items-center">
        <h4 class="fs-18 fw-700 lh-24 text-title-text">{{$pageTitle}}</h4>
        <button data-bs-toggle="modal" data-bs-target="#add-modal" class="flipBtn sf-flipBtn-primary flex-shrink-0">
            + {{__('Add Staff')}}</button>
    </div>
    <div class="p-sm-30 p-15">
        <div class="p-sm-25 p-15 bd-one bd-c-stroke bd-ra-10 bg-white">
            <div class="">
                <table class="table zTable zTable-last-item-right pt-15" id="staffDataTable">
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
                            <div>{{__('Roles')}}</div>
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
    <!-- Add Modal section start -->
    <div class="modal fade" id="add-modal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 bd-ra-4 p-sm-25 p-15">
                <div
                    class="bd-b-one bd-c-stroke pb-20 mb-20 d-flex align-items-center flex-wrap justify-content-between g-10">
                    <h2 class="fs-18 fw-600 lh-22 text-title-text">{{ __('Add Staff') }}</h2>
                    <div class="mClose">
                        <button type="button"
                                class="bd-one bd-c-stroke rounded-circle w-25 h-25 bg-transparent text-para-text"
                                data-bs-dismiss="modal" aria-label="Close">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                </div>
                <form class="ajax reset" action="{{ route('admin.staffs.store') }}" method="post"
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
                                <option value="{{STATUS_ACTIVE}}">{{ __('Active') }}</option>
                                <option value="{{STATUS_DEACTIVATE}}">{{ __('Deactivate') }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex flex-column g-15">
                        <div class="bd-b-one bd-c-stroke-2">
                            <h5 class="fs-16 fw-700 lh-24 text-title-text">{{ __('Permission roles') }}</h5>
                        </div>
                        <div class="d-flex flex-wrap g-10" id="staff-role-options">
                            @foreach($roles as $role)
                                <div class="staff-role-option">
                                    <div class="zCheck form-switch">
                                        <input class="form-check-input" type="checkbox" name="roles[]" id="staff-role-{{ $role->id }}" value="{{ $role->name }}" role="switch">
                                        <label class="zForm-label-alt" for="staff-role-{{ $role->id }}">{{ $role->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @if($roles->isEmpty())
                            <p class="small text-muted mb-0" id="staff-no-roles-note">{{ __('No active permission roles yet. Create one below to assign access to this staff account.') }}</p>
                        @endif
                        @if((int) auth()->user()->role === USER_ROLE_ADMIN)
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" id="toggle-staff-role-creator">
                                <i class="fa-solid fa-plus me-1" aria-hidden="true"></i>{{ __('Add custom role') }}
                            </button>
                        @endif
                    </div>
                    <div class="bd-c-stroke-2 justify-content-between align-items-center text-end pt-15">
                        <button type="submit" class="flipBtn sf-flipBtn-primary flex-shrink-0">
                            <div class="d-none h-25 spinner-border w-25" role="status">
                                <span class="visually-hidden"></span>
                            </div>
                            {{__('Submit')}}
                        </button>
                    </div>
                </form>
                @if((int) auth()->user()->role === USER_ROLE_ADMIN)
                    <section class="d-none mt-3 p-3 border rounded-3 bg-light" id="staff-role-creator" aria-labelledby="staff-role-creator-title">
                        <h3 class="fs-16 fw-700 mb-1" id="staff-role-creator-title">{{ __('Create a permission role') }}</h3>
                        <p class="small text-muted mb-3">{{ __('Create the role here, then configure module permissions such as Manage Admissions using the View, Add, Edit, and Delete matrix in Roles. It will be added to the staff form and selected automatically.') }}</p>
                        <form id="staff-role-creator-form" class="ajax" action="{{ route('admin.staffs.roles.store') }}" method="post" data-handler="staffRoleCreatedHandler">
                            @csrf
                            <input type="hidden" name="status" value="{{ STATUS_ACTIVE }}">
                            <div class="mb-3">
                                <label class="form-label" for="staff-custom-role-name">{{ __('Role name') }} <span class="text-danger">*</span></label>
                                <input id="staff-custom-role-name" class="form-control" name="name" maxlength="120" required placeholder="{{ __('e.g. Admissions Coordinator') }}">
                            </div>
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-light" id="cancel-staff-role-creator">{{ __('Cancel') }}</button>
                                <button type="submit" class="btn btn-primary">
                                    <span class="spinner-border spinner-border-sm d-none" aria-hidden="true"></span>{{ __('Save role') }}
                                </button>
                            </div>
                        </form>
                    </section>
                @endif
            </div>
        </div>
    </div>
    <!-- Add Modal section end -->
    <!-- Edit Modal section start -->
    <div class="modal fade" id="edit-modal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 bd-ra-4 p-sm-25 p-15">

            </div>
        </div>
    </div>
    <input type="hidden" id="staff-route" value="{{ route('admin.staffs.index') }}">

    <!-- Edit Modal section end -->
@endsection
@push('script')
    <script src="{{asset('admin/custom/js/staffs.js')}}?v=2"></script>
@endpush
