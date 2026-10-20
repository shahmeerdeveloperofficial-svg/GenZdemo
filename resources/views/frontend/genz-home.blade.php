@extends('frontend.layouts.app')

@push('title')
    {{ config('genz.name') }} — {{ config('genz.tagline') }}
@endpush

@section('content')
    <div class="genz-home">
        <section class="genz-hero">
            <div class="container">
                <div class="row align-items-center g-5">
                    <div class="col-lg-7">
                        <p class="genz-eyebrow"><span aria-hidden="true"></span>{{ __('Your global education partner') }}</p>
                        <h1>{{ __('Your study abroad journey, begins here.') }}</h1>
                        <p class="genz-hero__copy">{{ __('A clearer path from choosing a destination to preparing your application—with guidance from the GenZ Consultants team.') }}</p>
                        <div class="d-flex flex-wrap gap-3 mt-4">
                            <a class="genz-button-primary" href="{{ route('consultations.list') }}">{{ __('Book a consultation') }} <span aria-hidden="true">→</span></a>
                            <a class="genz-button-secondary" href="{{ route('universities.list') }}">{{ __('Explore universities') }}</a>
                            <a class="genz-button-secondary" href="{{ route('academic-programs.list') }}">{{ __('Browse study programs') }}</a>
                            <a class="genz-button-secondary" href="{{ route('study-abroad-registration.create') }}">{{ __('Student registration') }}</a>
                        </div>
                        @if ($countryData->count() || $universityData->count())
                            <div class="genz-proof-row" aria-label="{{ __('Current catalogue size') }}">
                                @if ($countryData->count())<span><strong>{{ $countryData->count() }}</strong> {{ __('study destinations') }}</span>@endif
                                @if ($universityData->count())<span><strong>{{ $universityData->count() }}</strong> {{ __('featured universities') }}</span>@endif
                            </div>
                        @endif
                    </div>
                    <div class="col-lg-5">
                        <div class="genz-hero-card">
                            <div class="genz-hero-card__icon" aria-hidden="true">✈</div>
                            <p class="genz-hero-card__label">{{ __('A plan built around you') }}</p>
                            <h2>{{ __('Start with a conversation. Move forward with a clear plan.') }}</h2>
                            <ol>
                                <li>{{ __('Tell us what you want to study') }}</li>
                                <li>{{ __('Compare destinations and options') }}</li>
                                <li>{{ __('Get support with your next steps') }}</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <form class="genz-search-panel" method="GET" action="{{ route('universities.list') }}" aria-label="{{ __('Find a university') }}">
                    <label for="genz-destination">{{ __('Where would you like to study?') }}</label>
                    <select id="genz-destination" name="country[]" class="form-select genz-field">
                        <option value="">{{ __('All destinations') }}</option>
                        @foreach ($countryData as $country)
                            <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endforeach
                    </select>
                    <button class="genz-button-primary" type="submit">{{ __('Search universities') }}</button>
                </form>
            </div>
        </section>

        <section class="genz-section" id="destinations">
            <div class="container">
                <div class="genz-section-heading">
                    <div><p class="genz-eyebrow">{{ __('Explore') }}</p><h2>{{ __('Study destinations') }}</h2><p>{{ __('Discover the places where your next chapter could begin.') }}</p></div>
                    <a href="{{ route('universities.list') }}" class="genz-text-link">{{ __('Explore destinations') }} <span aria-hidden="true">→</span></a>
                </div>
                @if ($countryData->isNotEmpty())
                    <div class="row g-3">
                        @foreach ($countryData->take(6) as $country)
                            <div class="col-12 col-sm-6 col-lg-4">
                                <a class="genz-destination-card" href="{{ route('country.details', $country->slug) }}">
                                    <span class="genz-destination-card__mark" aria-hidden="true">{{ strtoupper(mb_substr($country->name, 0, 1)) }}</span>
                                    <span><strong>{{ $country->name }}</strong><small>{{ __('Explore study options') }}</small></span>
                                    <span class="genz-destination-card__arrow" aria-hidden="true">↗</span>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="genz-empty-state"><strong>{{ __('Destination guides are being prepared.') }}</strong><span>{{ __('Contact our team and we can help you explore your options.') }}</span><a href="{{ route('contact-us') }}">{{ __('Talk to GenZ') }}</a></div>
                @endif
            </div>
        </section>

        <section class="genz-section genz-section--soft">
            <div class="container">
                <div class="genz-section-heading">
                    <div><p class="genz-eyebrow">{{ __('Find your fit') }}</p><h2>{{ __('Featured universities') }}</h2><p>{{ __('Explore institutions currently listed in our catalogue.') }}</p></div>
                    <a href="{{ route('universities.list') }}" class="genz-text-link">{{ __('View all universities') }} <span aria-hidden="true">→</span></a>
                </div>
                @if ($universityData->isNotEmpty())
                    <div class="row g-3">
                        @foreach ($universityData->take(6) as $university)
                            <div class="col-12 col-md-6 col-xl-4">
                                <article class="genz-university-card">
                                    <div class="genz-university-card__top">
                                        @if ($university->logo && getFileUrl($university->logo))
                                            <img src="{{ getFileUrl($university->logo) }}" alt="" loading="lazy" />
                                        @else
                                            <span class="genz-university-card__placeholder" aria-hidden="true">{{ strtoupper(mb_substr($university->name, 0, 1)) }}</span>
                                        @endif
                                        <span class="genz-university-card__country">{{ optional($countryData->firstWhere('id', $university->country_id))->name ?? __('Study abroad') }}</span>
                                    </div>
                                    <h3>{{ $university->name }}</h3>
                                    @if ($university->world_ranking)<p class="genz-university-card__meta">{{ __('World ranking') }}: {{ $university->world_ranking }}</p>@endif
                                    <a href="{{ route('universities.details', $university->slug) }}" class="genz-text-link">{{ __('View university') }} <span aria-hidden="true">→</span></a>
                                </article>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="genz-empty-state"><strong>{{ __('University listings are being updated.') }}</strong><span>{{ __('Ask us for guidance while the catalogue is prepared.') }}</span><a href="{{ route('consultations.list') }}">{{ __('Book a consultation') }}</a></div>
                @endif
            </div>
        </section>

        <section class="genz-section">
            <div class="container">
                <div class="genz-section-heading"><div><p class="genz-eyebrow">{{ __('How we help') }}</p><h2>{{ __('Support for the steps ahead') }}</h2><p>{{ __('Practical guidance for your study-abroad planning and application journey.') }}</p></div></div>
                @if ($serviceData->isNotEmpty())
                    <div class="row g-3">
                        @foreach ($serviceData->take(3) as $service)
                            <div class="col-12 col-md-6 col-lg-4">
                                <article class="genz-service-card">
                                    <span class="genz-service-card__icon" aria-hidden="true">✦</span>
                                    <h3>{{ $service->title }}</h3>
                                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($service->description), 150) }}</p>
                                    <a href="{{ route('service.details', $service->slug) }}" class="genz-text-link">{{ __('Learn more') }} <span aria-hidden="true">→</span></a>
                                </article>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="genz-empty-state"><strong>{{ __('Our consultancy services are being prepared.') }}</strong><span>{{ __('Get in touch to discuss the support you need.') }}</span><a href="{{ route('contact-us') }}">{{ __('Contact us') }}</a></div>
                @endif
            </div>
        </section>

        <section class="genz-consultation-band">
            <div class="container genz-consultation-band__inner">
                <div><p class="genz-eyebrow">{{ __('A good first step') }}</p><h2>{{ __('Let’s talk about your study-abroad goals.') }}</h2><p>{{ __('Meet with a consultant and get guidance tailored to your plans.') }}</p></div>
                <a class="genz-button-light" href="{{ route('consultations.list') }}">{{ __('Book a consultation') }} <span aria-hidden="true">→</span></a>
            </div>
        </section>

        @if ($eventData->isNotEmpty())
            <section class="genz-section">
                <div class="container"><div class="genz-section-heading"><div><p class="genz-eyebrow">{{ __('Meet and learn') }}</p><h2>{{ __('Upcoming events') }}</h2></div><a href="{{ route('event.list') }}" class="genz-text-link">{{ __('View all events') }} <span aria-hidden="true">→</span></a></div>
                    <div class="row g-3">@foreach ($eventData as $event)<div class="col-12 col-md-6 col-lg-4"><article class="genz-service-card"><p class="genz-eyebrow">{{ \Carbon\Carbon::parse($event->date_time)->format('M j, Y · g:i A') }}</p><h3>{{ $event->title }}</h3><p>{{ \Illuminate\Support\Str::limit(strip_tags($event->description), 130) }}</p><a href="{{ route('event.details', $event->slug) }}" class="genz-text-link">{{ __('Event details') }} <span aria-hidden="true">→</span></a></article></div>@endforeach</div>
                </div>
            </section>
        @endif

        @if ($courseData->isNotEmpty())
            <section class="genz-section genz-section--soft">
                <div class="container">
                    <div class="genz-section-heading"><div><p class="genz-eyebrow">{{ __('Learn online') }}</p><h2>{{ __('Courses to help you prepare') }}</h2><p>{{ __('Browse currently published learning options.') }}</p></div><a href="{{ route('courses.list') }}" class="genz-text-link">{{ __('Explore learning') }} <span aria-hidden="true">→</span></a></div>
                    <div class="row g-3">
                        @foreach ($courseData as $course)
                            <div class="col-12 col-md-6 col-lg-4"><article class="genz-course-card">
                                @if ($course->thumbnail)<img src="{{ getFileUrl($course->thumbnail) }}" alt="" loading="lazy" />@endif
                                <div><h3>{{ $course->title }}</h3><p>{{ \Illuminate\Support\Str::limit(strip_tags($course->subtitle ?: $course->description), 120) }}</p><a href="{{ route('courses.single', $course->slug) }}" class="genz-text-link">{{ __('View course') }} <span aria-hidden="true">→</span></a></div>
                            </article></div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if ($blogData->isNotEmpty())
            <section class="genz-section">
                <div class="container">
                    <div class="genz-section-heading"><div><p class="genz-eyebrow">{{ __('Guides and insights') }}</p><h2>{{ __('Latest from GenZ') }}</h2></div><a href="{{ route('blog.list') }}" class="genz-text-link">{{ __('View all articles') }} <span aria-hidden="true">→</span></a></div>
                    <div class="row g-3">
                        @foreach ($blogData->take(3) as $post)
                            <div class="col-12 col-md-6 col-lg-4"><article class="genz-article-card">
                                @if ($post->banner_image)<img src="{{ getFileUrl($post->banner_image) }}" alt="" loading="lazy" />@endif
                                <div><p class="genz-article-card__date">{{ $post->publish_date ? \Carbon\Carbon::parse($post->publish_date)->format('M j, Y') : '' }}</p><h3>{{ $post->title }}</h3><a href="{{ route('blog.details', $post->slug) }}" class="genz-text-link">{{ __('Read article') }} <span aria-hidden="true">→</span></a></div>
                            </article></div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if ($faqData->isNotEmpty())
            <section class="genz-section genz-section--soft">
                <div class="container"><div class="genz-section-heading"><div><p class="genz-eyebrow">{{ __('Good to know') }}</p><h2>{{ __('Frequently asked questions') }}</h2></div></div><div class="row g-3">@foreach ($faqData->take(6) as $faq)<div class="col-12 col-md-6"><details class="genz-contact-card"><summary class="fw-semibold">{{ $faq->question }}</summary><p class="mt-3 mb-0">{{ \Illuminate\Support\Str::limit(strip_tags($faq->answer), 600) }}</p></details></div>@endforeach</div></div>
            </section>
        @endif
    </div>
@endsection
