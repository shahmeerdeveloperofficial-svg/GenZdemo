@extends('layouts.app')
@push('title'){{ __('Preview lead import') }}@endpush
@section('content')
@php($validCount = count(array_filter($rows, fn ($row) => empty($row['errors']))))
<div class="p-sm-30 p-15"><div class="mb-4"><a href="{{ route($routePrefix . '.imports.create') }}">← {{ __('Choose another file') }}</a><h1 class="fs-24 fw-700 mt-3">{{ __('Review CSV rows') }}</h1><p>{{ __(':valid valid rows; :invalid invalid rows; :duplicates possible duplicates. Duplicate rows will merge into existing open leads at commit.', ['valid' => $validCount, 'invalid' => count(array_filter($rows, fn ($row) => !empty($row['errors']))), 'duplicates' => count(array_filter($rows, fn ($row) => !empty($row['duplicate'])) )]) }}</p></div>
<div class="table-responsive genz-contact-card p-0"><table class="table align-middle mb-0"><thead><tr><th>{{ __('CSV row') }}</th><th>{{ __('Name') }}</th><th>{{ __('Email') }}</th><th>{{ __('Phone') }}</th><th>{{ __('Preview result') }}</th></tr></thead><tbody>
@foreach ($rows as $row)<tr><td>{{ $row['row'] }}</td><td>{{ $row['first_name'] }} {{ $row['last_name'] }}</td><td>{{ $row['email'] }}</td><td>{{ $row['phone'] }}</td><td>@if ($row['errors'])<span class="text-danger">{{ implode(', ', $row['errors']) }}</span>@elseif ($row['duplicate'])<span class="text-warning">{{ __('Possible duplicate — will merge') }}</span>@else<span class="text-success">{{ __('Ready to import') }}</span>@endif</td></tr>@endforeach
</tbody></table></div>
<div class="d-flex gap-2 mt-4"><form method="POST" action="{{ route($routePrefix . '.imports.commit', $token) }}">@csrf<button class="genz-button-primary" @disabled($validCount === 0)>{{ __('Import valid rows') }}</button></form><a href="{{ route($routePrefix . '.index') }}" class="genz-button-light">{{ __('Cancel') }}</a></div>
</div>
@endsection
