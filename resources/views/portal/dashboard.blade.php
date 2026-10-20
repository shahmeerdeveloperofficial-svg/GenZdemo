@extends('layouts.app')

@push('title'){{ $pageTitle }}@endpush

@section('content')
    <main class="container-fluid p-sm-30 p-15" aria-labelledby="portal-heading">
        <header class="mb-4">
            <p class="text-muted mb-1">{{ __('GenZ Consultants workspace') }}</p>
            <h1 id="portal-heading" class="h3 mb-2">{{ __('Welcome') }}, {{ auth()->user()->first_name }}</h1>
            <p class="mb-0">{{ __('Your role-specific') }} {{ strtolower($roleName) }} {{ __('overview') }}.</p>
        </header>

        <section class="row g-3" aria-label="{{ __('Overview metrics') }}">
            @foreach ($metrics as $metric)
                <div class="col-12 col-sm-6 col-xl-3">
                    <article class="genz-card h-100 p-4">
                        <p class="text-muted mb-2">{{ $metric['label'] }}</p>
                        <p class="h2 mb-0">{{ number_format($metric['value']) }}</p>
                    </article>
                </div>
            @endforeach
        </section>

        @if (collect($metrics)->every(fn ($metric) => (int) $metric['value'] === 0))
            <section class="genz-card mt-4 p-4" aria-live="polite">
                <h2 class="h5">{{ __('Nothing assigned yet') }}</h2>
                <p class="mb-0 text-muted">{{ __('When records are assigned to your account, they will appear here. No sample statistics are shown.') }}</p>
            </section>
        @endif
    </main>
@endsection
