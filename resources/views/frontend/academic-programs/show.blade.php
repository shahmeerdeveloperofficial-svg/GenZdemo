@extends('frontend.layouts.app')

@section('title', $program->meta_title ?: $program->title.' | GenZ Consultants')
@section('meta_description', $program->meta_description ?: 'Explore '.$program->title.' at '.$program->university->name.'. Confirm requirements and dates with the institution.')

@section('content')
<main class="container py-5" style="max-width: 1000px">
    <nav aria-label="Breadcrumb" class="small mb-4"><a href="{{ route('academic-programs.list') }}">Programs</a> / {{ $program->country->name }}</nav>
    <p class="text-info fw-semibold">{{ $program->country->name }}@if($program->university->city) · {{ $program->university->city }}@endif</p>
    <h1 class="display-5 fw-bold">{{ $program->title }}</h1>
    <p class="fs-5">{{ $program->university->name }}@if($program->studyLevel) · {{ $program->studyLevel->name }}@endif</p>
    @if($program->degree_award || $program->duration)<p class="text-muted">{{ collect([$program->degree_award, $program->duration])->filter()->implode(' · ') }}</p>@endif
    @if($program->description)<section class="my-4"><h2 class="h4">About this program</h2><p class="text-body-secondary">{{ $program->description }}</p></section>@endif
    @if($program->entry_requirements)<section class="my-4"><h2 class="h4">Entry requirements</h2><ul>@foreach($program->entry_requirements as $requirement)<li>{{ $requirement }}</li>@endforeach</ul></section>@endif
    <section class="my-4"><h2 class="h4">Intakes</h2>
        @forelse($program->intakes as $intake)<div class="border rounded-3 p-3 mb-2"><strong>{{ $intake->label }}</strong>@if($intake->start_date) · Starts {{ $intake->start_date->format('M Y') }}@endif@if($intake->application_deadline) · Apply by {{ $intake->application_deadline->format('M d, Y') }}@endif</div>@empty<p class="text-muted">Current intake dates are not confirmed yet. Contact us for guidance.</p>@endforelse
    </section>
    <aside class="alert alert-info">Requirements, availability and deadlines can change. Confirm directly with the university before making an application decision.</aside>
    @if($program->source_url)<p class="small">Program information: <a href="{{ $program->source_url }}" target="_blank" rel="noopener noreferrer">official source</a>@if($program->last_verified_at) · Checked {{ $program->last_verified_at->format('M d, Y') }}@endif</p>@endif
    @if($program->university->source_url)<p class="small">Institution information: <a href="{{ $program->university->source_url }}" target="_blank" rel="noopener noreferrer">official source</a></p>@endif
    <a class="btn btn-primary" href="{{ route('contact-us') }}">Talk to a counsellor</a>
</main>
@endsection
