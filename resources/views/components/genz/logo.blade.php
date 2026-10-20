@props([
    'href' => null,
    'inverse' => false,
    'compact' => false,
])

@php($classes = 'genz-brand' . ($inverse ? ' genz-brand--inverse' : '') . ($compact ? ' genz-brand--compact' : ''))
@php($logoFile = public_path('genz/images/genz-consultants-logo.png'))

<a {{ $attributes->class($classes) }} href="{{ $href ?: url('/') }}" aria-label="{{ config('genz.name') }} — {{ config('genz.tagline') }}">
    @if (is_file($logoFile))
        <img class="genz-brand__image" src="{{ asset('genz/images/genz-consultants-logo.png') }}" alt="GenZ Consultants — Global Education and Career Development" loading="eager" fetchpriority="high" decoding="async" />
    @endif
        <span class="genz-brand__fallback">
            <span class="genz-brand__wordmark">Gen<span class="genz-brand__z">Z</span></span>
            @unless($compact)
                <span class="genz-brand__descriptor">{{ config('genz.tagline') }}</span>
            @endunless
        </span>
</a>
