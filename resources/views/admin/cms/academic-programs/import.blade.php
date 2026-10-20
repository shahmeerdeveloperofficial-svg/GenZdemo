@extends('layouts.app')
@push('title') Import Study Programs @endpush
@section('content')
<div class="p-sm-30 p-15"><a href="{{ route('admin.cms-settings.academic-programs.index') }}">← Programs</a><h1 class="fs-24 fw-700 mt-3">Preview program import</h1><p class="text-muted">Upload CSV (max 2 MB, 500 rows). Preview validates every row and writes nothing. Required headers are shown below; requirements can be separated by |.</p>
@if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
<code class="d-block p-3 bg-light rounded mb-3">title,slug,university_id,country_id,study_level_id,subject_id,degree_award,duration,description,entry_requirements,source_url,last_verified_at,status</code>
<form method="post" enctype="multipart/form-data" action="{{ route('admin.cms-settings.academic-programs.import.preview') }}">@csrf<label class="form-label">CSV file</label><input class="form-control" type="file" accept=".csv,text/csv" name="file" required><button class="btn btn-primary mt-3">Preview import</button></form></div>
@endsection
