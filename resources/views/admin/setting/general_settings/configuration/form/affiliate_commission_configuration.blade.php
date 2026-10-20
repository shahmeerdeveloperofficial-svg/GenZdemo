<div class="customers__area">
    <div class="bd-b-one bd-c-stroke pb-20 mb-20 d-flex align-items-center flex-wrap justify-content-between g-10">
        <h2 class="fs-18 fw-600 lh-22 text-title-text">{{ __('Affiliate Commission Configuration') }}</h2>
        <div class="mClose">
            <button type="button" class="bd-one bd-c-stroke rounded-circle w-25 h-25 bg-transparent text-para-text"
                data-bs-dismiss="modal" aria-label="Close">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
    </div>
    <form class="ajax" action="{{ route('admin.setting.settings_env.update') }}" method="post" class="form-horizontal"
        data-handler="commonResponseForModal">
        @csrf
        <div class="row">
            <div class="col-12 mb-4">
                <h6 class="mb-3">{{ __('Service-Specific Commission Rates & Levels') }}</h6>
                <small
                    class="text-muted d-block mb-3">{{ __('Set commission rates and levels for each service type separately.') }}</small>

                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">{{ __('Courses') }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="zForm-label-alt">{{ __('Commission Rate (%)') }}</label>
                                    <input type="number" class="form-control zForm-control-alt"
                                        name="affiliate_commission_course_rate"
                                        value="{{ getOption('affiliate_commission_course_rate', 10) }}" min="0"
                                        max="100" step="0.01" placeholder="10" required />
                                </div>
                                <div class="mb-3">
                                    <label class="zForm-label-alt">{{ __('Number of Levels') }}</label>
                                    <input type="number" class="form-control zForm-control-alt"
                                        name="affiliate_commission_course_levels"
                                        value="{{ getOption('affiliate_commission_course_levels', 1) }}" min="1"
                                        max="10" step="1" placeholder="1" />
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        onchange="changeSettingStatus(this,'affiliate_commission_on_course')"
                                        value="1" id="course_commission"
                                        {{ getOption('affiliate_commission_on_course', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="course_commission">
                                        {{ __('Enable Commissions') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">{{ __('Services') }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="zForm-label-alt">{{ __('Commission Rate (%)') }}</label>
                                    <input type="number" class="form-control zForm-control-alt"
                                        name="affiliate_commission_service_rate"
                                        value="{{ getOption('affiliate_commission_service_rate', 8) }}" min="0"
                                        max="100" step="0.01" placeholder="8" required />
                                </div>
                                <div class="mb-3">
                                    <label class="zForm-label-alt">{{ __('Number of Levels') }}</label>
                                    <input type="number" class="form-control zForm-control-alt"
                                        name="affiliate_commission_service_levels"
                                        value="{{ getOption('affiliate_commission_service_levels', 1) }}"
                                        min="1" max="10" step="1" placeholder="1" />
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        onchange="changeSettingStatus(this,'affiliate_commission_on_service')"
                                        value="1" id="service_commission"
                                        {{ getOption('affiliate_commission_on_service', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="service_commission">
                                        {{ __('Enable Commissions') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4 mb-4">
                        <div class="card border">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">{{ __('Consultations') }}</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="zForm-label-alt">{{ __('Commission Rate (%)') }}</label>
                                    <input type="number" class="form-control zForm-control-alt"
                                        name="affiliate_commission_consultation_rate"
                                        value="{{ getOption('affiliate_commission_consultation_rate', 12) }}"
                                        min="0" max="100" step="0.01" placeholder="12" required />
                                </div>
                                <div class="mb-3">
                                    <label class="zForm-label-alt">{{ __('Number of Levels') }}</label>
                                    <input type="number" class="form-control zForm-control-alt"
                                        name="affiliate_commission_consultation_levels"
                                        value="{{ getOption('affiliate_commission_consultation_levels', 1) }}"
                                        min="1" max="10" step="1" placeholder="1" />
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox"
                                        onchange="changeSettingStatus(this,'affiliate_commission_on_consultation')"
                                        value="1" id="consultation_commission"
                                        {{ getOption('affiliate_commission_on_consultation', 1) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="consultation_commission">
                                        {{ __('Enable Commissions') }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="col-12 mb-4">
                <div class="alert alert-info">
                    <h6><i class="fa-solid fa-info-circle me-2"></i>{{ __('How Commission Works') }}</h6>
                    <ul class="mb-0 mt-2">
                        <li>{{ __('Commission = Purchase Amount × Affiliate Rate ÷ 100') }}</li>
                        <li>{{ __('Affiliates can set their own rates (0-100%) in their profile') }}</li>
                        <li>{{ __('Only enabled services will generate commissions') }}</li>
                        <li>{{ __('Commissions are automatically calculated and recorded on purchases') }}</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="d-flex g-12 flex-wrap mt-25">
            <button class="flipBtn sf-flipBtn-primary" type="submit">
                <span>{{ __('Update Settings') }}</span>
                <span>{{ __('Update Settings') }}</span>
                <span>{{ __('Update Settings') }}</span>
            </button>
        </div>
    </form>
</div>

<script>
    $(document).ready(function() {
        // Commission toggles are handled via AJAX, form only handles rates/levels
    });
</script>

<style>
    .commission-level-row {
        border-left: 3px solid #007bff;
        padding-left: 10px;
        background-color: #f8f9fa;
        border-radius: 4px;
    }
</style>
