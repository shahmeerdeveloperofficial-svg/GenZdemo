<div class="invoice-content">
    <div class="bd-b-one bd-c-stroke pb-25 mb-25 d-flex justify-content-between align-items-center flex-wrap">
        <!--  -->
        <a href="" data-bs-dismiss="modal" aria-label="Close"
           class="d-inline-flex align-items-center cg-13 fs-18 fw-500 lh-22 text-brand-primary">
            <i class="fa-solid fa-long-arrow-left"></i>
            {{__('Back')}}
        </a>

        <!--  -->
        <a target="blank" href="{{ route('admin.payouts.print', $withdraw->id) }}"
           class="flipBtn sf-flipBtn-primary">
            <span>{{__('Download')}}</span>
            <span>{{__('Download')}}</span>
            <span>{{__('Download')}}</span>
        </a>
    </div>
    <!--  -->
    <div class="d-flex justify-content-between align-items-center pb-50">
        <!--  -->
        <div class="max-w-167">
            <img src="{{ getSettingImage('app_logo_black') }}" alt="{{ getOption('app_name') }}"/>
        </div>
        <!--  -->

        <div class="d-flex align-items-center cg-10">
            @if ($withdraw->status == WITHDRAWAL_STATUS_COMPLETE)
                <p class="bd-ra-5 py-4 px-14 zBadge-active fs-12 fw-500 lh-24 text-green">{{__('Paid')}}</p>
            @elseif ($withdraw->status == WITHDRAWAL_STATUS_PENDING)
                <p class="bd-ra-5 py-4 px-14 zBadge-pending fs-12 fw-500 lh-24">{{__('Pending')}}</p>
            @elseif ($withdraw->status == WITHDRAWAL_STATUS_REJECTED)
                <p class="bd-ra-5 py-4 px-14 zBadge-cancel fs-12 fw-500 lh-24">{{__('Rejected')}}</p>
            @endif
        </div>

    </div>
    <!--  -->
    <div class="bd-ra-10 bg-secondary p-25 mb-30">
        <div class="row">
            <div class="col-md-4">
                <h4 class="fs-27 fw-600 lh-40 text-title-text pb-10">{{__('Payout Receipt')}}</h4>
                <p class="fs-15 fw-500 lh-20 text-title-text">{{ $withdraw->transection_id ?: '' }}</p>
            </div>
            <div class="col-md-4">
                <p class="fs-14 fw-600 lh-24 text-para-text">{{__('User')}}:</p>
                <p class="fs-14 fw-400 lh-24 text-para-text">{{ $withdraw->user?->name }}</p>
                <p class="fs-14 fw-400 lh-24 mailto:text-para-text">{{ $withdraw->user?->email }}</p>
                <p class="fs-14 fw-400 lh-24 text-para-text">{{__('Balance')}}: {{ showPrice(int_to_decimal($withdraw->user?->balance)) }}</p>
            </div>
            <div class="col-md-4">
                <p class="fs-14 fw-600 lh-24 text-para-text">{{__('Gateway')}}:</p>
                <p class="fs-14 fw-400 lh-24 text-para-text">{{ getBeneficiaryName($withdraw->beneficiary?->type) }}</p>
                <p class="fs-14 fw-600 lh-24 text-para-text">{{__('Beneficiary')}}:</p>
                <p class="fs-14 fw-400 lh-24 text-para-text">{{ $withdraw->beneficiary?->beneficiary_name }}</p>
                <p class="fs-14 fw-600 lh-24 text-para-text">{{__('Details')}}:</p>
                <p class="fs-14 fw-400 lh-24 text-para-text">{!! getBeneficiaryAccountDetails($withdraw->beneficiary) !!}</p>
            </div>
        </div>
    </div>
    <!--  -->

    <div class="pb-15">

        <div class="table-responsive pb-15">
            <table class="table zTable zTable-last-item-right zTable-last-item-border">


            </table>
        </div>
    </div>

    <!--  -->
    <div class="max-w-374 w-100 ms-auto mb-30 text-end invoiceTotal">
        <ul class="zList-pb-15">
            <li>
                <div class="row align-items-center">
                    <div class="col-6">
                        <p class="fs-14 fw-500 lh-17 text-para-text">{{__('Total')}}:</p>
                    </div>
                    <div class="col-6">
                        <p class="fs-14 fw-600 lh-17 text-title-text">{{ showPrice($withdraw->amount) }}</p>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <!--  -->
    <h4 class="fs-18 fw-600 lh-28 text-title-text pb-15">{{__('Transaction Details')}}</h4>
    <table class="table zTable zTable-last-item-right zTable-last-item-border">
        <thead>
        <tr>
            <th>
                <div>{{__("Date")}}</div>
            </th>
            <th>
                <div>{{__("Payment Gateway")}}</div>
            </th>
            <th>
                <div>{{__("Transaction ID")}}</div>
            </th>
            <th>
                <div>{{__("Amount")}}</div>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{{ $withdraw->created_at->format('Y-m-d H:i:s') }}</td>
            <td>{{ getBeneficiaryName($withdraw->beneficiary?->type) }}</td>
            <td>{{ $withdraw->transection_id }}</td>
            <td>{{ showPrice($withdraw->amount) }}</td>
        </tr>
        </tbody>
    </table>

        @if($withdraw->narration)
        <h4 class="fs-18 fw-600 lh-28 text-title-text pb-15">{{__('Narration')}}</h4>
        <p>{{ $withdraw->narration }}</p>
    @endif
</div>
