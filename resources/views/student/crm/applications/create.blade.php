@extends('layouts.app')
@push('title') Start an Application @endpush
@section('content')
<div class="p-sm-30 p-15"><a href="{{ route('student.admissions.index') }}">← My applications</a><h1 class="fs-24 fw-700 mt-3">Start a university application</h1><p class="text-muted">This creates a draft only. It does not submit an application to a university.</p>
@if($errors->any())<div class="alert alert-danger">@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>@endif
<form class="bg-white p-4 rounded-3" method="post" action="{{ route('student.admissions.store') }}">@csrf
<div class="mb-3"><label class="form-label" for="program">Program</label><select class="form-select" id="program" name="program_id" required><option value="">Choose a program</option>@foreach($programs as $program)<option value="{{ $program->id }}" @selected(old('program_id')==$program->id)>{{ $program->title }} — {{ $program->university->name }}, {{ $program->country->name }}</option>@endforeach</select></div>
<div class="mb-3"><label class="form-label" for="intake">Intake (optional)</label><select class="form-select" id="intake" name="intake_id"><option value="">Decide later</option>@foreach($programs as $program)@foreach($program->intakes as $intake)<option data-program="{{ $program->id }}" value="{{ $intake->id }}" @selected(old('intake_id')==$intake->id)>{{ $program->title }} — {{ $intake->label }}@if($intake->application_deadline) (deadline {{ $intake->application_deadline->format('M d, Y') }})@endif</option>@endforeach @endforeach</select></div>
<div class="alert alert-info">{{ __('A counselor will review your draft with you. Deadlines, eligibility and admissions decisions are determined by the institution.') }}</div><button class="btn btn-primary">Create draft application</button></form></div>
<script>const p=document.getElementById('program'),i=document.getElementById('intake');function filterIntakes(){for(const o of i.options){if(!o.dataset.program)continue;o.hidden=o.dataset.program!==p.value;if(o.hidden&&o.selected)i.value=''}}p.addEventListener('change',filterIntakes);filterIntakes();</script>
@endsection
