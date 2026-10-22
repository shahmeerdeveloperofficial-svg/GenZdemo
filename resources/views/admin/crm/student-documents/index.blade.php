@extends('layouts.app')
@push('title'){{ __('Student document review') }}@endpush
@section('content')
<div class="p-sm-30 p-15">
    <header class="mb-4">
        <p class="text-muted mb-1">{{ __('Private documents shared by students with their assigned team.') }}</p>
        <h1 class="fs-24 fw-700">{{ __('Student documents') }}</h1>
        <p class="text-muted mb-0">{{ __('Students upload files in their portal under Study profile & documents > Your documents > Submit for review.') }}</p>
    </header>
    @include('partials.list-criteria')
    <div class="table-responsive genz-contact-card p-0">
        <table class="table align-middle mb-0">
            <thead><tr><th>{{ __('Student') }}</th><th>{{ __('Branch') }}</th><th>{{ __('Document') }}</th><th>{{ __('Uploaded') }}</th><th>{{ __('Status') }}</th><th>{{ __('Secure file') }}</th><th>{{ __('Review') }}</th></tr></thead>
            <tbody>
                @forelse ($documents as $document)
                    <tr>
                        <td>
                            @if($document->student)<a href="{{ route($routePrefix . '.profile', $document->student) }}">{{ $document->student->name }}</a>@else{{ __('Student account unavailable') }}@endif
                            <small class="d-block text-muted">{{ $document->student?->email }}</small>
                        </td>
                        <td>{{ $document->student?->branch ?: __('Not assigned') }}</td>
                        <td>{{ __(\Illuminate\Support\Str::headline($document->category)) }}<small class="d-block text-muted">{{ $document->original_name }} &middot; {{ $document->size_bytes < 1024 ? $document->size_bytes . ' B' : number_format($document->size_bytes / 1024, 1) . ' KB' }}</small></td>
                        <td>{{ $document->created_at->format('M j, Y g:i A') }}</td>
                        <td><span class="badge {{ $document->status === 'approved' ? 'bg-success' : ($document->status === 'rejected' ? 'bg-danger' : 'bg-secondary') }}">{{ __(ucfirst($document->status)) }}</span></td>
                        <td><a href="{{ route($routePrefix . '.download', $document) }}" class="genz-text-link">{{ __('Download') }}</a></td>
                        <td>
                            @if($document->status === 'pending')
                                <form method="POST" action="{{ route($routePrefix . '.review', $document) }}" class="genz-document-review">
                                    @csrf
                                    <select name="status" class="form-select" aria-label="{{ __('Review decision') }}"><option value="approved">{{ __('Approve') }}</option><option value="rejected">{{ __('Reject') }}</option></select>
                                    <input name="review_note" maxlength="2000" class="form-control" aria-label="{{ __('Review note') }}" placeholder="{{ __('Required for rejection') }}">
                                    <button class="btn btn-sm btn-primary">{{ __('Save review') }}</button>
                                </form>
                            @else
                                <span class="text-muted">{{ $document->review_note ?: __('Review complete') }}</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-5">{{ __('No documents match these filters.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $documents->links() }}</div>
</div>
@endsection
