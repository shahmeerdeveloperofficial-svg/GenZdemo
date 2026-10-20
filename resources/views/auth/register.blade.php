@extends('layouts.app')

@push('title')
    {{ __('Registration') }}
@endpush

@section('content')
    <div class="signLog-section">
        <div class="right" data-background="{{ getFileUrl(getOption('login_right_image')) }}">
            <x-genz.logo :href="route('frontend')" class="logo" />
            <h4 class="title">
                {{ getOption('auth_page_title') }}
            </h4>
        </div>
        <div class="left">
            <div class="wrap">
                <div class="zMain-signLog-content">
                    <div class="pb-sm-35 pb-20">
                        <h4 class="fs-sm-36 fs-26 fw-600 lh-sm-46 lh-20 text-title-text pb-5">{{ __('Create An Account') }}
                        </h4>
                        <p class="fs-18 fw-500 lh-28 text-para-text">{{ __('Already have an account?') }}
                            <a href="{{ route('login') }}" class="text-brand-primary">{{ __('Sign In') }}</a>
                        </p>
                    </div>
                    <form action="{{ route('register') }}" enctype="multipart/form-data" method="post">
                        @csrf
                        <input type="hidden" name="affiliate_referral_key" id="affiliate_referral_key" value="">
                        <div class="row rg-25 pb-15">
                            <div class="col-md-6">
                                <div class="">
                                    <label for="inputFullName" class="zForm-label">{{ __('First Name') }}</label>
                                    <input type="text" class="form-control zForm-control" id="inputFullName"
                                        name="first_name" placeholder="{{ __('Enter First name') }}"
                                        value="{{ old('first_name') }}" />
                                    @error('first_name')
                                        <span class="fs-12 text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="">
                                    <label for="inputFullName" class="zForm-label">{{ __('Last Name') }}</label>
                                    <input type="text" class="form-control zForm-control" id="inputFullName"
                                        name="last_name" placeholder="{{ __('Enter Last name') }}"
                                        value="{{ old('last_name') }}" />
                                    @error('last_name')
                                        <span class="fs-12 text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="">
                                    <label for="inputEmailAddress" class="zForm-label">{{ __('Email Address') }}</label>
                                    <input type="email" class="form-control zForm-control" id="inputEmailAddress"
                                        value="{{ old('email') }}" name="email"
                                        placeholder="{{ __('Enter email address') }}" />
                                    @error('email')
                                        <span class="fs-12 text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="0">
                                    <label for="inputPassword" class="zForm-label">{{ __('Password') }}</label>
                                    <div class="passShowHide">
                                        <input type="password" class="form-control zForm-control passShowHideInput"
                                            id="inputPassword" placeholder="{{ __('Enter your password') }}"
                                            name="password" />
                                        <button type="button" toggle=".passShowHideInput"
                                            class="toggle-password fa-solid fa-eye"></button>
                                        @error('password')
                                            <span class="fs-12 text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="pb-sm-35 pb-20 d-flex justify-content-start align-items-center flex-wrap g-10">
                            <div class="zForm-wrap-checkbox">
                                <input type="checkbox" class="form-check-input" name="agree_policy" id="termsPrivacy" />
                                <label
                                    for="termsPrivacy">{{ __('By clicking Create account, I agree that I have read and
                                                                                                                                                                                                                                                                                                                                                                                                                                                accepted the') }}
                                    <a href="{{ route('page', 'page-terms_services_page') }}"
                                        target="__blank">{{ __('Terms of Use') }}</a> {{ __('and') }} <a
                                        href="{{ route('page', 'page-privacy_policy_page') }}"
                                        target="__blank">{{ __('Privacy
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            Policy') }}</a>.</label>
                            </div>
                        </div>
                        <!--  -->
                        <button type="submit" class="flipBtn sf-flipBtn-primary w-100">{{ __('Sign Up') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Retrieve affiliate referral link from local storage and cookies, populate hidden field
        document.addEventListener('DOMContentLoaded', function() {
            let referralKey = null;

            // First try localStorage
            if (typeof(Storage) !== 'undefined') {
                referralKey = localStorage.getItem('affiliate_referral_key');
                if (referralKey) {
                    document.getElementById('affiliate_referral_key').value = referralKey;
                    return; // Found in localStorage, no need to check cookies
                }
            }

            // Fallback to server-set cookies
            @if (isset($_COOKIE['affiliate_referral_key']))
                referralKey = '{{ $_COOKIE['affiliate_referral_key'] }}';
                if (referralKey) {
                    document.getElementById('affiliate_referral_key').value = referralKey;
                    // Also save to localStorage for future use
                    if (typeof(Storage) !== 'undefined') {
                        localStorage.setItem('affiliate_referral_key', referralKey);
                    }
                    return;
                }
            @endif

            // Final fallback to JS-set cookies
            function getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
                return null;
            }

            referralKey = getCookie('affiliate_referral_key_js');
            if (referralKey) {
                document.getElementById('affiliate_referral_key').value = referralKey;
                // Save to localStorage for future use
                if (typeof(Storage) !== 'undefined') {
                    localStorage.setItem('affiliate_referral_key', referralKey);
                }
            }
        });
    </script>
@endsection
