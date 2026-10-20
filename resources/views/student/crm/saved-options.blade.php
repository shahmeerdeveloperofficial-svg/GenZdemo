@extends('layouts.app')
@push('title') Saved Study Options @endpush
@section('content')
<div class="p-sm-30 p-15"><header class="mb-4"><h1 class="fs-24 fw-700">Saved study options</h1><p class="text-muted">Your private shortlist of active destinations, institutions, programs and scholarships.</p></header>
<div class="row g-3">@forelse($saved as $option)@php($item=$option->item)<div class="col-md-6 col-xl-4"><article class="p-4 bg-white border rounded-4 h-100"><p class="small text-info text-uppercase">{{ str_replace('_',' ',$option->item_type) }}</p><h2 class="h5">{{ $item->title ?? $item->name }}</h2><form method="post" action="{{ route('student.saved-options.destroy',$option) }}">@csrf @method('DELETE')<button class="btn btn-outline-danger btn-sm">Remove</button></form></article></div>@empty<div class="col-12"><div class="p-5 bg-white rounded-4 text-center"><p class="mb-0">No saved options yet. Add programs or destinations while browsing.</p></div></div>@endforelse</div></div>
@endsection
