@extends('layouts.app')
@push('title') Study Abroad Programs @endpush
@section('content')
<div class="p-sm-30 p-15">
    <header class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="fs-24 fw-700">Study Abroad Programs</h1><p class="text-muted">Academic catalog (separate from LMS courses). Publishing requires an official source and verification date.</p></div><div class="d-flex gap-2"><a class="btn btn-outline-primary" href="{{ route('admin.cms-settings.academic-programs.import') }}">Import CSV</a><a class="btn btn-primary" href="{{ route('admin.cms-settings.academic-programs.create') }}">Add program</a></div></header>
    @include('partials.list-criteria')
    <div class="table-responsive bg-white rounded-3"><table class="table align-middle"><thead><tr><th>Program</th><th>University</th><th>Country</th><th>Level</th><th>Status</th><th>Verified</th><th></th></tr></thead><tbody>
    @forelse($programs as $program)<tr><td>{{ $program->title }}</td><td>{{ $program->university->name }}</td><td>{{ $program->country->name }}</td><td>{{ $program->studyLevel?->name ?? '—' }}</td><td>{{ ucfirst($program->status) }}</td><td>{{ $program->last_verified_at?->format('M d, Y') ?? '—' }}</td><td class="text-nowrap"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.cms-settings.academic-programs.edit', $program) }}">Edit</a> <form class="d-inline" method="post" action="{{ route('admin.cms-settings.academic-programs.destroy', $program) }}" onsubmit="return confirm('Archive this program?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Archive</button></form></td></tr>@empty<tr><td colspan="7" class="text-center py-4">No programs created yet.</td></tr>@endforelse
    </tbody></table></div>{{ $programs->links() }}
</div>
@endsection
