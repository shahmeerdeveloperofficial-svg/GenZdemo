@extends('layouts.app')
@push('title') Manage Unavailable Dates @endpush
@section('content')
<div class="p-sm-30 p-15"><h1 class="fs-24 fw-700">Unavailable dates</h1><p class="text-muted">Students cannot book on these dates. Your weekly days off are managed in your consultant profile.</p>
@if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
<form class="row g-2 bg-white p-3 rounded-3 mb-3" method="post" action="{{ route('consultant.availability.blackouts.store') }}">@csrf<div class="col-md-4"><label class="form-label">Date</label><input class="form-control" name="date" type="date" min="{{ today()->format('Y-m-d') }}" required></div><div class="col-md-6"><label class="form-label">Reason (private)</label><input class="form-control" name="reason" maxlength="500"></div><div class="col-md-2 align-self-end"><button class="btn btn-primary">Add date</button></div></form>
<div class="table-responsive bg-white"><table class="table"><thead><tr><th>Date</th><th>Private note</th><th></th></tr></thead><tbody>@forelse($dates as $date)<tr><td>{{ $date->date->format('M d, Y') }}</td><td>{{ $date->reason }}</td><td><form method="post" action="{{ route('consultant.availability.blackouts.destroy',$date) }}">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Remove</button></form></td></tr>@empty<tr><td colspan="3" class="text-center py-4">No extra unavailable dates.</td></tr>@endforelse</tbody></table></div>{{ $dates->links() }}</div>
@endsection
