<div class="bd-b-one bd-c-stroke pb-20 mb-20 d-flex align-items-center flex-wrap justify-content-between g-10">
    <h2 class="fs-18 fw-600 lh-22 text-title-text">{{ __('Update Payout Status') }}</h2>
    <div class="mClose">
        <button type="button"
                class="bd-one bd-c-stroke rounded-circle w-25 h-25 bg-transparent text-para-text"
                data-bs-dismiss="modal" aria-label="Close">
            <i class="fa-solid fa-times"></i>
        </button>
    </div>
</div>
<!-- User Info Section -->
<div class="mb-20">
    <h4 class="fs-16 fw-600 lh-24 text-title-text mb-15">{{ __('User Information') }}</h4>
    <div class="row rg-15">
        <div class="col-md-6">
            <p class="fs-14 fw-600 lh-24 text-para-text">{{ __('Name') }}:</p>
            <p class="fs-14 fw-400 lh-24 text-para-text">{{ $withdraw->user?->name }}</p>
        </div>
        <div class="col-md-6">
            <p class="fs-14 fw-600 lh-24 text-para-text">{{ __('Email') }}:</p>
            <p class="fs-14 fw-400 lh-24 text-para-text">{{ $withdraw->user?->email }}</p>
        </div>
        <div class="col-md-4">
            <p class="fs-14 fw-600 lh-24 text-para-text">{{ __('Balance Before') }}:</p>
            <p class="fs-14 fw-400 lh-24 text-para-text">
                @if($withdraw->status == WITHDRAWAL_STATUS_PENDING || $withdraw->status == WITHDRAWAL_STATUS_COMPLETE)
                    {{ showPrice(int_to_decimal($withdraw->user?->balance + decimal_to_int($withdraw->amount))) }}
                @else
                    {{ showPrice(int_to_decimal($withdraw->user?->balance)) }}
                @endif
            </p>
        </div>
        <div class="col-md-4">
            <p class="fs-14 fw-600 lh-24 text-para-text">{{ __('Withdrawal Amount') }}:</p>
            <p class="fs-14 fw-400 lh-24 text-para-text">{{ showPrice($withdraw->amount) }}</p>
        </div>
        <div class="col-md-4">
            <p class="fs-14 fw-600 lh-24 text-para-text">{{ __('Current Balance ') }}:</p>
            <p class="fs-14 fw-400 lh-24 text-para-text">{{ showPrice(int_to_decimal($withdraw->user?->balance)) }}</p>
        </div>
    </div>
</div>

<!-- Beneficiary Info Section -->
<div class="mb-20">
    <h4 class="fs-16 fw-600 lh-24 text-title-text mb-15">{{ __('Beneficiary Information') }}</h4>
    <div class="row rg-15">
        <div class="col-md-6">
            <p class="fs-14 fw-600 lh-24 text-para-text">{{ __('Beneficiary Name') }}:</p>
            <p class="fs-14 fw-400 lh-24 text-para-text">{{ $withdraw->beneficiary?->beneficiary_name }}</p>
        </div>
        <div class="col-md-6">
            <p class="fs-14 fw-600 lh-24 text-para-text">{{ __('Type') }}:</p>
            <p class="fs-14 fw-400 lh-24 text-para-text">{{ getBeneficiaryName($withdraw->beneficiary?->type) }}</p>
        </div>
        <div class="col-md-12">
            <p class="fs-14 fw-600 lh-24 text-para-text">{{ __('Details') }}:</p>
            <p class="fs-14 fw-400 lh-24 text-para-text">{!! getBeneficiaryAccountDetails($withdraw->beneficiary) !!}</p>
        </div>
    </div>
</div>

<form class="ajax reset" action="{{ route('admin.payouts.status_change', $withdraw->id) }}" method="post"
      data-handler="commonResponseForModal">
    @csrf
    <div class="row rg-20 mb-20">
        <div class="col-md-12">
            <label for="status" class="zForm-label-alt">{{ __('Status') }} <span class="text-danger">*</span></label>
            <select class="sf-select-without-search" id="status" name="status">
                <option value="{{ WITHDRAWAL_STATUS_PENDING }}" {{ $withdraw->status == WITHDRAWAL_STATUS_PENDING ? 'selected' : '' }}>{{ __('Pending') }}</option>
                <option value="{{ WITHDRAWAL_STATUS_COMPLETE }}" {{ $withdraw->status == WITHDRAWAL_STATUS_COMPLETE ? 'selected' : '' }}>{{ __('Approved') }}</option>
                <option value="{{ WITHDRAWAL_STATUS_REJECTED }}" {{ $withdraw->status == WITHDRAWAL_STATUS_REJECTED ? 'selected' : '' }}>{{ __('Rejected') }}</option>
            </select>
        </div>

        <div class="col-md-12">
            <label for="narration" class="zForm-label-alt">{{ __('Note') }}</label>
            <textarea class="form-control zForm-control-alt" id="narration" name="narration" rows="3" placeholder="{{ __('Enter note (optional)') }}"></textarea>
        </div>
    </div>
    <div class="bd-c-stroke-2 justify-content-between align-items-center text-end pt-15">
        <button type="submit" class="flipBtn sf-flipBtn-primary flex-shrink-0">
            <div class="spinner-border w-25 h-25 ml-10 d-none" role="status">
                <span class="visually-hidden"></span>
            </div>
            <div class="innerWrap">
                <span>{{__('Update')}}</span>
                <span>{{__('Update')}}</span>
                <span>{{__('Update')}}</span>
            </div>
        </button>
    </div>
</form>

