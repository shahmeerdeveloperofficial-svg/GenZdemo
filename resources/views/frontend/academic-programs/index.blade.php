@extends('frontend.layouts.app')

@section('title', 'Study Abroad Programs | GenZ Consultants')

@section('content')
<main class="container py-5">
    <header class="mb-4">
        <p class="text-uppercase small fw-bold text-info mb-2">Find your next step</p>
        <h1 class="display-5 fw-bold">Study abroad programs</h1>
        <p class="text-muted">Explore published programs and verify current entry requirements and deadlines with the institution.</p>
    </header>
    <form method="get" class="row g-2 p-3 mb-4 rounded-4 bg-light" aria-label="Filter programs">
        <div class="col-12 col-md-4"><input class="form-control" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Program, university or country" maxlength="100"></div>
        <div class="col-6 col-md-2"><select class="form-select" name="country"><option value="">All countries</option>@foreach($countries as $country)<option value="{{ $country->id }}" @selected(($filters['country'] ?? '') == $country->id)>{{ $country->name }}</option>@endforeach</select></div>
        <div class="col-6 col-md-2"><select class="form-select" name="university"><option value="">All universities</option>@foreach($universities as $university)<option value="{{ $university->id }}" @selected(($filters['university'] ?? '') == $university->id)>{{ $university->name }}</option>@endforeach</select></div>
        <div class="col-6 col-md-2"><select class="form-select" name="level"><option value="">Any level</option>@foreach($levels as $level)<option value="{{ $level->id }}" @selected(($filters['level'] ?? '') == $level->id)>{{ $level->name }}</option>@endforeach</select></div>
        <div class="col-6 col-md-2"><select class="form-select" name="subject"><option value="">Any subject</option>@foreach($subjects as $subject)<option value="{{ $subject->id }}" @selected(($filters['subject'] ?? '') == $subject->id)>{{ $subject->name }}</option>@endforeach</select></div>
        <div class="col-6 col-md-2"><select class="form-select" name="sort"><option value="title">Sort: title</option><option value="university" @selected(($filters['sort'] ?? '') === 'university')>University</option><option value="intake" @selected(($filters['sort'] ?? '') === 'intake')>Upcoming intake</option></select></div>
        <div class="col-6 col-md-2"><button class="btn btn-primary w-100">Search</button></div>
    </form>
    @if($errors->has('comparison'))<div class="alert alert-warning">{{ $errors->first('comparison') }}</div>@endif
    <p class="text-muted">{{ $programs->total() }} programs found · <a href="{{ route('academic-programs.compare') }}">Compare selected programs</a></p>
    <div class="row g-3">
        @forelse($programs as $program)
            <div class="col-12 col-md-6 col-xl-4"><article class="card h-100 border-0 shadow-sm rounded-4"><div class="card-body p-4">
                <p class="small text-info fw-semibold mb-2">{{ $program->country->name }}@if($program->university->city) · {{ $program->university->city }}@endif</p>
                <h2 class="h5 fw-bold"><a class="text-decoration-none text-dark" href="{{ route('academic-programs.details', $program->slug) }}">{{ $program->title }}</a></h2>
                <p class="mb-1">{{ $program->university->name }}</p>
                <p class="small text-muted">{{ $program->studyLevel?->name }}@if($program->degree_award) · {{ $program->degree_award }}@endif</p>
                @if($program->intakes->isNotEmpty())<p class="small"><strong>Upcoming intakes:</strong> {{ $program->intakes->take(2)->pluck('label')->implode(', ') }}</p>@else<p class="small text-muted">Intake dates need confirmation</p>@endif
                <a class="btn btn-outline-primary btn-sm" href="{{ route('academic-programs.details', $program->slug) }}">View program</a>
                @auth<form class="d-inline" method="post" action="{{ route('student.saved-options.store') }}">@csrf<input type="hidden" name="item_type" value="academic_program"><input type="hidden" name="item_id" value="{{ $program->id }}"><button class="btn btn-outline-secondary btn-sm">Save</button></form>@endauth
                <form class="d-inline" method="post" action="{{ route('academic-programs.compare.add') }}">@csrf<input type="hidden" name="program_id" value="{{ $program->id }}"><button class="btn btn-outline-secondary btn-sm">Compare</button></form>
            </div></article></div>
        @empty
            <div class="col-12"><div class="p-5 text-center rounded-4 bg-light"><h2 class="h4">No published programs yet</h2><p class="mb-0 text-muted">Our team is verifying program information. Please check back soon or contact us for guidance.</p></div></div>
        @endforelse
    </div>
    <div class="mt-4">{{ $programs->links() }}</div>
</main>
@endsection
