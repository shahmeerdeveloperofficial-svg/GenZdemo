@extends('layouts.app')
@push('title') Confirm Program Import @endpush
@section('content')
<div class="p-sm-30 p-15"><h1 class="fs-24 fw-700">Import preview</h1><p>{{ count($rows) }} rows parsed. Nothing has been written yet.</p>
@if($errors)<div class="alert alert-danger"><strong>Resolve these row errors before importing:</strong><ul class="mb-0">@foreach($errors as $error)<li>{{ $error }}</li>@endforeach</ul></div>@else<div class="alert alert-success">All rows passed validation. Confirm to save them atomically.</div>@endif
<div class="table-responsive bg-white"><table class="table"><thead><tr><th>Row</th><th>Program</th><th>University ID</th><th>Country ID</th><th>Status</th><th>Source</th></tr></thead><tbody>@foreach($rows as $row)<tr><td>{{ $row['_line'] }}</td><td>{{ $row['title'] }}</td><td>{{ $row['university_id'] }}</td><td>{{ $row['country_id'] }}</td><td>{{ $row['status'] }}</td><td>{{ $row['source_url'] }}</td></tr>@endforeach</tbody></table></div>
<form method="post" action="{{ route('admin.cms-settings.academic-programs.import.confirm') }}">@csrf<input type="hidden" name="token" value="{{ $token }}"><button class="btn btn-primary" @disabled($errors)>Confirm and import</button> <a class="btn btn-outline-secondary" href="{{ route('admin.cms-settings.academic-programs.import') }}">Upload corrected file</a></form></div>
@endsection
