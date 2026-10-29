@extends('layouts.app')
@push('title')
    {{ __('Role permissions') }}
@endpush
@section('content')
    <div class="px-sm-30 p-15 bd-b-one bd-c-stroke-2 d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fs-18 fw-700 lh-24 text-title-text">{{ $roleLabel }} — {{ __('permissions') }}</h4>
            <p class="mb-0 text-muted">{{ __('Admin is always Super Admin and cannot be restricted here.') }}</p>
        </div>
        <a href="{{ route('admin.staffs.roles.index') }}" class="flipBtn sf-flipBtn-primary">{{ __('Back to roles') }}</a>
    </div>
    <div class="p-sm-30 p-15">
        <form action="{{ route('admin.staffs.roles.system.update', $role->name ? str_replace('__system_', '', $role->name) : '') }}" method="post" class="p-sm-25 p-15 bd-one bd-c-stroke bd-ra-10 bg-white">
            @csrf
            @method('PATCH')
            <h5 class="fs-16 fw-700 lh-24 text-title-text">{{ __('Module permissions') }}</h5>
            <p class="small text-muted">{{ __('Choose View, Add, Edit, and Delete permissions for each module. Changes are enforced on protected pages and actions.') }}</p>
            @include('admin.staffs.roles._permission_matrix')
            <div class="text-end"><button type="submit" class="flipBtn sf-flipBtn-primary">{{ __('Save permissions') }}</button></div>
        </form>
    </div>
@endsection
