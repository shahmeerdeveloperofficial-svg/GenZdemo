<div class="row">
    <div class="col-md-12">
        <div class="mClose text-end">
            <button type="button" class="bd-one bd-c-stroke rounded-circle w-25 h-25 bg-transparent text-para-text"
                data-bs-dismiss="modal" aria-label="Close">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
        <div class="item-top p-4 text-center">
            <h1 class="p-2">{{ __('Affiliate Commission Settings') }}</h1><br>
            <hr>
            <div class="text-start">
                <h3 class="p-2">
                    {{ __('Configure dynamic affiliate commission rates with multi-level support for different services.') }}
                </h3>
                <br>
                <h5>{{ __('Features:') }}</h5>
                <ul class="text-start list-unstyled">
                    <li><i
                            class="fa-solid fa-check text-success me-2"></i>{{ __('Service-specific commission rates (Courses, Services, Consultations)') }}
                    </li>
                    <li><i
                            class="fa-solid fa-check text-success me-2"></i>{{ __('Multi-level commission system (1-10 levels)') }}
                    </li>
                    <li><i
                            class="fa-solid fa-check text-success me-2"></i>{{ __('Enable/disable commission for each service type') }}
                    </li>
                    <li><i
                            class="fa-solid fa-check text-success me-2"></i>{{ __('Affiliates can set their own custom rates') }}
                    </li>
                    <li><i
                            class="fa-solid fa-check text-success me-2"></i>{{ __('Automatic commission calculation on purchases') }}
                    </li>
                    <li><i class="fa-solid fa-check text-success me-2"></i>{{ __('Commission tracking and history') }}
                    </li>
                </ul>
                <br>
                <h5>{{ __('How it works:') }}</h5>
                <ol class="text-start">
                    <li>{{ __('Set commission rates for each service type (Courses, Services, Consultations)') }}</li>
                    <li>{{ __('Set number of commission levels for each service separately (1-10)') }}</li>
                    <li>{{ __('Enable/disable commission for each service type') }}</li>
                    <li>{{ __('Affiliates share their referral links') }}</li>
                    <li>{{ __('When users make purchases, commissions are automatically calculated based on service rates') }}
                    </li>
                    <li>{{ __('Multi-level commissions are paid based on each service\'s level settings') }}</li>
                    <li>{{ __('All commissions are added to affiliate wallets') }}</li>
                </ol>
                <br>
                <h5>{{ __('Commission Priority:') }}</h5>
                <ul class="text-start">
                    <li><strong>{{ __('Affiliate Custom Rate') }}</strong> →
                        {{ __('Highest priority (if set by affiliate)') }}</li>
                    <li><strong>{{ __('Service-Specific Rate') }}</strong> →
                        {{ __('Required rate for each service type') }}
                    </li>
                </ul>
                <br>
                <h5>{{ __('Service-Specific Levels:') }}</h5>
                <p>{{ __('Each service (Courses, Services, Consultations) can have different numbers of commission levels. For example, Courses might have 3 levels while Services have 2 levels.') }}
                </p>
            </div>
            <br>
            <a class="sf-btn-primary" target="_blank" href="https://studylifter-doc.zainikthemes.com">
                <span>{{ __('View the documentation') }}</span>
            </a>
        </div>
    </div>
</div>
