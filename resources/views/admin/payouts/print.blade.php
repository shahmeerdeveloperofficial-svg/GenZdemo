<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/css/dataTables.responsive.min.css') }}"/>
    <link rel="stylesheet" href="{{ asset('assets/scss/style.css') }}"/>

    <style>
        @page {
            margin: 0px;
            padding: 0px;
        }

        body {
            margin: 0px;
            padding: 0px;
        }

        img {
            width: 120px;
        }

        * {
            overflow: hidden;
        }
    </style>
</head>

<body>
<div class="modal-xl mx-auto m-5">
    <div class="row">
        <div class="col-md-12">
            <div class="p-3 bg-white rounded">
                <div class="px-20" id="printableArea">

                    <!--  -->
                    <div class="d-flex justify-content-between align-items-center pb-50 pt-20">
                        <!--  -->
                        <div class="max-w-167">
                            <img src="{{ getSettingImage('app_logo_black') }}" alt="{{ getOption('app_name') }}"/></div>
                        <!--  -->
                        <div class="d-flex align-items-center cg-10">
                            @if ($withdraw->status == WITHDRAWAL_STATUS_COMPLETE)
                                <p class="bd-ra-5 py-4 px-14 zBadge-active fs-12 fw-500 lh-24 text-green">
                                    {{ __('Paid') }}
                                </p>
                            @elseif ($withdraw->status == WITHDRAWAL_STATUS_PENDING)
                                <p class="bd-ra-5 py-4 px-14 zBadge-pending fs-12 fw-500 lh-24">{{ __('Pending') }}
                                </p>
                            @elseif ($withdraw->status == WITHDRAWAL_STATUS_REJECTED)
                                <p class="bd-ra-5 py-4 px-14 zBadge-cancel fs-12 fw-500 lh-24">{{ __('Rejected') }}
                                </p>
                            @endif
                        </div>
                    </div>
                    <!--  -->
                    <div class="bd-ra-10 bg-secondary p-25 mb-30">
                        <div class="d-flex justify-content-between invoice-item">
                            <div class="item">
                                <h4 class="fs-27 fw-600 lh-40 text-title-text pb-10">{{__('Payout Receipt')}}</h4>
                                <p class="fs-15 fw-500 lh-20 text-title-text"> {{ $withdraw->transection_id }}</p>
                            </div>
                            <div class="item">
                                <p class="fs-14 fw-600 lh-24 text-para-text">{{__('User')}}:</p>
                                <p class="fs-14 fw-400 lh-24 text-para-text">{{ $withdraw->user?->name }}</p>
                                <p class="fs-14 fw-400 lh-24 mailto:text-para-text">{{ $withdraw->user?->email }}</p>
                            </div>
                            <div class="item">
                                <p class="fs-14 fw-600 lh-24 text-para-text">{{__('Gateway')}}:</p>
                                <p class="fs-14 fw-400 lh-24 text-para-text">{{ getBeneficiaryName($withdraw->beneficiary?->type) }}</p>
                            </div>
                        </div>
                    </div>



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
            </div>
        </div>
    </div>
</div>
<script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
<script src="{{ asset('admin/custom/js/print-invoice.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
</body>

</html>
