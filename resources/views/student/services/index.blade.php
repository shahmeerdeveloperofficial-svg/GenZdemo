@extends('layouts.app')
@push('title'){{ $pageTitle }}@endpush
@section('content')
<div class="p-sm-30 p-15 pb-0">
    <header class="d-flex align-items-center justify-content-between mb-4"><h1 class="fs-24 fw-700">{{ $pageTitle }}</h1></header>
    @include('partials.list-criteria')
</div>
<div class="p-15 p-sm-30 pt-0" id="service-list-container">
    @include('admin.services.partials.list')
</div>
<input type="hidden" id="my-service-route" value="{{ url()->current() }}">
@endsection
@push('script')
<script src="{{ asset('admin/custom/js/services.js') }}?v=2"></script>
@endpush
