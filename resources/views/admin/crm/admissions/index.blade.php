@extends('layouts.app')
@push('title') Admissions @endpush
@section('content')
<div class="p-sm-30 p-15"><h1 class="fs-24 fw-700">Admissions pipeline</h1><p class="text-muted">Every transition is recorded. University decisions remain external and are not guaranteed.</p>
@include('partials.list-criteria')
<div class="table-responsive bg-white rounded-3"><table class="table align-middle"><thead><tr><th>Reference</th><th>Student</th><th>Program</th><th>University</th><th>Stage</th><th>Deadline</th><th>{{ __('Actions') }}</th></tr></thead><tbody>@forelse($applications as $application)<tr><td>{{ $application->reference_code }}</td><td>{{ $application->student->first_name }} {{ $application->student->last_name }}</td><td>{{ $application->program->title }}</td><td>{{ $application->university->name }}</td><td>{{ str_replace('_',' ',ucfirst($application->status)) }}</td><td>{{ $application->application_deadline?->format('M d, Y') ?? '—' }}</td><td><a class="genz-row-action genz-row-action--edit" href="{{ route($routePrefix.'.show',$application) }}" title="{{ __('Edit application') }}" aria-label="{{ __('Edit application') }}"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i></a></td></tr>@empty<tr><td colspan="7" class="text-center py-4">No applications match.</td></tr>@endforelse</tbody></table></div>{{ $applications->links() }}</div>
@endsection
