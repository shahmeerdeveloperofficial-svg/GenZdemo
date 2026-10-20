@extends('layouts.app')
@push('title'){{ __('Add lead') }}@endpush
@section('content')
<div class="p-sm-30 p-15"><div class="mb-4"><a href="{{ route($routePrefix . '.index') }}">← {{ __('Back to leads') }}</a><h1 class="fs-24 fw-700 mt-3">{{ __('Add a lead') }}</h1></div>
<form method="POST" action="{{ route($routePrefix . '.store') }}" class="genz-contact-card row g-3">@csrf
    <div class="col-md-6"><label class="form-label">{{ __('First name') }} *</label><input name="first_name" value="{{ old('first_name') }}" required maxlength="120" class="form-control genz-field">@error('first_name')<small class="text-danger">{{ $message }}</small>@enderror</div>
    <div class="col-md-6"><label class="form-label">{{ __('Last name') }}</label><input name="last_name" value="{{ old('last_name') }}" maxlength="120" class="form-control genz-field"></div>
    <div class="col-md-6"><label class="form-label">{{ __('Email') }}</label><input type="email" name="email" value="{{ old('email') }}" maxlength="255" class="form-control genz-field">@error('email')<small class="text-danger">{{ $message }}</small>@enderror</div>
    <div class="col-md-6"><label class="form-label">{{ __('Phone') }}</label><input type="tel" name="phone" value="{{ old('phone') }}" maxlength="60" class="form-control genz-field"></div>
    <div class="col-md-6"><label class="form-label">{{ __('Lead source') }} *</label><select name="source" class="form-select genz-field" required>@foreach (['walk_in', 'phone', 'referral', 'website', 'campaign', 'manual'] as $source)<option value="{{ $source }}" @selected(old('source', 'manual') === $source)>{{ __(\Illuminate\Support\Str::headline($source)) }}</option>@endforeach</select></div>
    <div class="col-md-6"><label class="form-label">{{ __('Source details') }}</label><input name="source_detail" value="{{ old('source_detail') }}" maxlength="255" class="form-control genz-field"></div>
    @if ((int) auth()->user()->role !== USER_ROLE_CONSULTANT)<div class="col-md-6"><label class="form-label">{{ __('Assign counselor') }}</label><select name="assigned_to" class="form-select genz-field"><option value="">{{ __('Unassigned') }}</option>@foreach ($owners as $owner)<option value="{{ $owner->id }}" @selected(old('assigned_to') == $owner->id)>{{ $owner->name }}</option>@endforeach</select></div>@endif
    <div class="col-md-6"><label class="form-label">{{ __('Tags (comma separated)') }}</label><input name="tags" value="{{ old('tags') }}" maxlength="500" class="form-control genz-field"></div>
    <div class="col-12"><label class="form-label">{{ __('Internal note') }}</label><textarea name="note" rows="3" maxlength="10000" class="form-control genz-field">{{ old('note') }}</textarea></div>
    <div class="col-12"><button class="genz-button-primary" type="submit">{{ __('Save lead') }}</button></div>
</form></div>
@endsection
