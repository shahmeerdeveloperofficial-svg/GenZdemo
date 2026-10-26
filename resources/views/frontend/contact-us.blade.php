@extends('frontend.layouts.app')
@push('title')
    {{ __('Home') }}
@endpush
@section('content')
    @php($contact = config('genz.contact'))
    @php($publicEmail = $contact['is_demo'] ? $contact['email'] : (getOption('app_email') ?: $contact['email']))
    @php($publicPhone = $contact['is_demo'] ? $contact['phone'] : (getOption('app_contact_number') ?: $contact['phone']))
    @php($publicAddress = $contact['is_demo'] ? $contact['address'] : (getOption('app_location') ?: $contact['address']))
    @php($messageLabel = __('Message'))
    <!-- Breadcrumb -->
    <section class="inner-breadcrumb-section">
        <div class="container">
            <div class="inner-breadcrumb-wrap" data-aos="fade-up" data-aos-duration="1000">
                <h4 class="inner-breadcrumb-title">Contact Us</h4>
                <ol class="breadcrumb inner-breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('frontend')}}">{{__('Home')}}</a></li>
                    <li class="breadcrumb-item"><a href="{{route('frontend')}}">{{__('Contact Us')}}</a></li>
                </ol>
            </div>
        </div>
    </section>
    <!-- About destination -->
    <section class="section-gap-top">
        <div class="container">
            <div class="row g-4 align-items-start">
                <aside class="col-lg-4">
                    <div class="genz-contact-card">
                        <p class="genz-eyebrow">{{ __('Talk to our team') }}</p>
                        <h2 class="h4">{{ __('We’re here to help you plan your next step.') }}</h2>
                        @if ($contact['is_demo'])<p class="genz-contact-demo-note mt-3">{{ __('Sample contact information for demonstration only. Replace before launch.') }}</p>@endif
                        <p class="mt-4 mb-1"><strong>{{ __('Email') }}</strong></p>
                        <a href="mailto:{{ $publicEmail }}">{{ $publicEmail }}</a>
                        <p class="mt-4 mb-1"><strong>{{ __('Phone / WhatsApp') }}</strong></p>
                        @if ($contact['is_demo'])<span>{{ $publicPhone }}</span>@else<a href="tel:{{ preg_replace('/[^+0-9]/', '', $publicPhone) }}">{{ $publicPhone }}</a>@endif
                        @if (!empty($contact['whatsapp']))<div class="mt-2"><a class="genz-text-link" href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contact['whatsapp']) }}" target="_blank" rel="noopener noreferrer">{{ __('Message us on WhatsApp') }} <span aria-hidden="true">↗</span></a></div>@endif
                        <p class="mt-4 mb-1"><strong>{{ __('Office') }}</strong></p>
                        <p class="mb-1">{{ $publicAddress }}</p>
                        <p class="text-muted mb-0">{{ $contact['hours'] }}</p>
                    </div>
                </aside>
                <div class="col-lg-8">
            <form class="ajax genz-contact-card" action="{{ route('contact-us.store') }}" method="post"
                  data-handler="commonResponseWithPageLoad">
                @csrf
                <input type="hidden" name="recaptcha_token" id="recaptcha_token">
                <div class="contact-us-content" data-aos="fade-up" data-aos-duration="1000">
                    <div class="pb-md-40 pb-30">
                        <h4 class="section-title text-center pb-0">{{__('Get in Touch')}}</h4>
                    </div>
                    <div class="row rg-30">
                        <div class="col-md-6">
                            <label class="zForm-label" for="contactUsFirstName">{{__('First Name')}} <span class="text-danger">*</span></label>
                            <input type="text" class="zForm-control genz-field" name="first_name" autocomplete="given-name" maxlength="255" required placeholder="{{__('Enter First Name')}}" id="contactUsFirstName">
                        </div>
                        <div class="col-md-6">
                            <label class="zForm-label" for="contactUsLastName">{{__('Last Name')}} <span class="text-danger">*</span></label>
                            <input type="text" class="zForm-control genz-field" name="last_name" autocomplete="family-name" maxlength="255" required placeholder="{{__('Enter Last Name')}}" id="contactUsLastName">
                        </div>
                        <div class="col-md-6">
                            <label class="zForm-label" for="contactUsMobileNumber">{{__('Mobile Number')}} <span class="text-danger">*</span></label>
                            <input type="tel" class="zForm-control genz-field" name="mobile" autocomplete="tel" maxlength="60" required placeholder="{{__('Enter Mobile Number')}}" id="contactUsMobileNumber">
                        </div>
                        <div class="col-md-6">
                            <label class="zForm-label" for="contactUsEmailAddress">{{__('Email Address')}} <span class="text-danger">*</span></label>
                            <input type="email" class="zForm-control genz-field" name="email" autocomplete="email" maxlength="255" required placeholder="{{__('Enter Email Address')}}" id="contactUsEmailAddress">
                        </div>
                        <div class="col-12">
                            <label class="zForm-label" for="contactUsMessage">{{ is_string($messageLabel) ? $messageLabel : 'Message' }} <span class="text-danger">*</span></label>
                            <textarea class="zForm-control genz-field contactUsMessage bd-ra-10" id="contactUsMessage" name="message" required maxlength="5000" placeholder="{{__('Leave your message')}}"></textarea>
                        </div>
                        <div class="col-12">
                            <div class="pt-10">
                                <button type="submit" class="sf-btn-icon-primary">
                                    {{__('Submit Now')}}
                                    <span class="icon">
                                        <img src="{{ asset('assets/images/icon/icon-angle-right.svg') }}" alt="">
                                        <img src="{{ asset('assets/images/icon/icon-angle-right.svg') }}" alt="">
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
                </div>
            </div>
        </div>
    </section>
    @if(getOption('enable_recaptcha'))
        <div class="g-recaptcha mt-20" data-sitekey={{getOption('recaptcha_key')}}></div>
        @error('g-recaptcha-response')
        <span class="fs-12 text-danger">{{ $message }}</span>
        @enderror
    @endif
@endsection
@push('script')
    @if (!empty(getOption('recapture_in_contact_us')) && getOption('recapture_in_contact_us') == 1)
        <script
            src="https://www.google.com/recaptcha/api.js?render={{ getOption('google_recaptcha_site_key') }}"></script>
        <script>
            grecaptcha.ready(function () {
                grecaptcha.execute('{{ getOption('google_recaptcha_site_key') }}', {action: 'submit'}).then(function (token) {
                    document.getElementById('recaptcha_token').value = token;
                });
            });
        </script>
    @endif
@endpush
