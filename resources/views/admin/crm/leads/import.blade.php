@extends('layouts.app')
@push('title'){{ __('Import leads') }}@endpush
@section('content')
<div class="p-sm-30 p-15"><div class="mb-4"><a href="{{ route($routePrefix . '.index') }}">← {{ __('Back to leads') }}</a><h1 class="fs-24 fw-700 mt-3">{{ __('Import leads from CSV') }}</h1><p class="text-muted">{{ __('Preview and correct rows before anything is saved. Email is the primary duplicate key; phone is the fallback.') }}</p></div>
<form method="POST" enctype="multipart/form-data" action="{{ route($routePrefix . '.imports.preview') }}" class="genz-contact-card">@csrf
    <label class="form-label" for="lead-csv">{{ __('CSV file') }}</label><input id="lead-csv" name="file" type="file" accept=".csv,text/csv" class="form-control genz-field" required>
    @error('file')<p class="text-danger mt-2">{{ $message }}</p>@enderror
    <p class="text-muted mt-3">{{ __('Required headers: first_name and at least one of email or phone. Optional: last_name, email, phone. Up to 500 rows and 2 MB.') }}</p>
    <button type="submit" class="genz-button-primary">{{ __('Preview import') }}</button>
</form></div>
@endsection
