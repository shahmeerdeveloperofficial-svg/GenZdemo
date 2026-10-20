@extends('layouts.app')
@push('title')
    {{ __('My Wallet') }}
@endpush


@section('content')
    <div class="p-sm-30 p-15">

        <!-- Affiliate Stats Section -->
        <div class="pb-md-40 pb-20">
            <div class="row rg-20">
                <div class="col-xl-2 col-md-4 col-sm-6">
                    <div class="home-card-item">
                         <div class="d-flex justify-content-between align-items-center">
                             <h4 class="no">
                                 @if (get_currency_placement() == 'after')
                                     {{ getNumberFormat(userBalance()) }} {{ get_currency_symbol() }}
                                 @else
                                     {{ get_currency_symbol() }} {{ getNumberFormat(userBalance()) }}
                                 @endif
                             </h4>
                             <div class="icon"><img src="{{ asset('assets/images/icon/referrals.svg') }}" alt="" />
                             </div>
                         </div>
                         <p class="info">{{ __('My Balance') }}</p>
                     </div>
                </div>


            </div>
        </div>



        <!-- Wallet Tabs Section -->
        <div class="p-sm-25 p-15 bd-one bd-c-stroke bd-ra-10 bg-white">
            <!-- Button and Tabs Section -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <ul class="nav nav-tabs mb-0" id="walletTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="transaction-tab" data-bs-toggle="tab"
                            data-bs-target="#transaction-tab-pane" type="button" role="tab"
                            aria-controls="transaction-tab-pane" aria-selected="true">
                            {{ __('Earning History') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="beneficiary-tab" data-bs-toggle="tab"
                            data-bs-target="#beneficiary-tab-pane" type="button" role="tab"
                            aria-controls="beneficiary-tab-pane" aria-selected="false">
                            {{ __('Beneficiary') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="withdrawal-tab" data-bs-toggle="tab"
                            data-bs-target="#withdrawal-tab-pane" type="button" role="tab"
                            aria-controls="withdrawal-tab-pane" aria-selected="false">
                            {{ __('Withdrawal History') }}
                        </button>
                    </li>

                </ul>

                <div class="btn-group">
                    <button type="button" class="flipBtn sf-flipBtn-primary" data-bs-toggle="modal"
                        data-bs-target="#add-modal">
                        + {{ __('Add Beneficiary') }}
                    </button>
                    <button type="button" class="flipBtn sf-flipBtn-primary" data-bs-toggle="modal"
                        data-bs-target="#withdrawalModal">
                        {{ __('Request a Withdrawal') }}
                    </button>

                </div>
            </div>

            <!-- Common Search Box -->
            <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                <div class="tableFilter-wrap flex-grow-1 me-3">
                    <div class="item">
                        <label for="wallet_search_key" class="zForm-label-alt">{{ __('Search') }}</label>
                        <input type="text" class="form-control zForm-control-alt" name="wallet_search_key"
                            id="wallet_search_key"
                            placeholder="{{ __('Search By Transaction ID, Amount, Date, Type') }}" />
                    </div>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="tab-content" id="walletTabsContent">
                <!-- Transaction History Tab -->
                <div class="tab-pane fade show active" id="transaction-tab-pane" role="tabpanel"
                    aria-labelledby="transaction-tab" tabindex="0">
                    <table id="my-earnings" class="table zTable zTable-last-item-right">
                        <thead>
                            <tr>
                                <th>
                                    <div>{{ __('Date') }}</div>
                                </th>
                                <th>
                                    <div>{{ __('Transaction ID') }}</div>
                                </th>
                                <th>
                                    <div>{{ __('Description') }}</div>
                                </th>
                                <th>
                                    <div>{{ __('Type ') }}</div>
                                </th>
                                <th>
                                    <div>{{ __('Commission ') }}</div>
                                </th>
                                <th>
                                    <div>{{ __('Amount') }}</div>
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <!-- Withdrawal History Tab -->
                <div class="tab-pane fade" id="withdrawal-tab-pane" role="tabpanel" aria-labelledby="withdrawal-tab"
                    tabindex="0">
                    <table id="withdrawalHistory" class="table zTable zTable-last-item-right">
                        <thead>
                            <tr>
                                <th>
                                    <div>{{ __('Date') }}</div>
                                </th>
                                <th>
                                    <div>{{ __('Transaction ID') }}</div>
                                </th>
                                <th>
                                    <div>{{ __('Description') }}</div>
                                </th>
                                <th>
                                    <div>{{ __('Gateway') }}</div>
                                </th>
                                <th>
                                    <div>{{ __('Amount') }}</div>
                                </th>
                                <th>
                                    <div>{{ __('Status') }}</div>
                                </th>
                            </tr>
                        </thead>
                    </table>
                </div>

                <!-- Beneficiary Tab -->
                <div class="tab-pane fade" id="beneficiary-tab-pane" role="tabpanel" aria-labelledby="beneficiary-tab"
                    tabindex="0">
                    <div class="">
                        <table id="customers-table" class="table zTable zTable-last-item-right pt-15">

                           <thead>
                               <tr>
                                   <th>
                                       <div>{{ __('Name') }}</div>
                                   </th>
                                   <th>
                                       <div>{{ __('Type') }}</div>
                                   </th>
                                   <th>
                                       <div>{{ __('Details') }}</div>
                                   </th>
                                   <th>
                                       <div>{{ __('Status') }}</div>
                                   </th>
                                   <th>
                                       <div>{{ __('Action') }}</div>
                                   </th>
                               </tr>
                           </thead>
                           <tbody>
                           </tbody>
                       </table>
                   </div>
                </div>
            </div>
        </div>


    </div>



    <!--Withdrawal Modal Start-->
    <div class="modal fade" id="withdrawalModal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 bd-ra-4">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-sm-25 p-15">
                    <div class="withdrawal-modal-title text-center">
                        <p class="font-13 font-medium">{{ __('Available Balance') }}</p>
                        <h4>
                            @if (get_currency_placement() == 'after')
                                {{ getNumberFormat(userBalance()) }} {{ get_currency_symbol() }}
                            @else
                                {{ get_currency_symbol() }} {{ getNumberFormat(userBalance()) }}
                            @endif
                        </h4>
                    </div>
                    <form method="POST" action="{{ route('affiliate.wallet.process-withdraw') }}">
                        @csrf
                        <div class="row">

                            <div class="col-md-12 mb-30">
                                <div class="label-text-title color-heading font-medium font-16 mb-3">{{ __('Amount') }}
                                    <span class="text-danger">*</span>
                                </div>
                                <input type="number" name="amount" min="1" class="form-control"
                                    placeholder="{{ __('Type amount') }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-30">
                                <label class="font-medium font-15 color-heading">{{ __('Beneficiary') }}<span
                                        class="text-danger">*</span></label>
                                <select name="uuid" class="form-select" required>
                                    @foreach ($beneficiaries as $beneficiary)
                                        <option value="{{ $beneficiary->uuid }}">
                                            @if($beneficiary->type == BENEFICIARY_BANK)
                                                {{ __('Bank') }}: {{ $beneficiary->bank_name }} | {{ __('A/C') }}: {{ $beneficiary->bank_account_number }} | {{ __('Routing') }}: {{ $beneficiary->bank_routing_number }} | {{ __('Account Name') }}: {{ $beneficiary->bank_account_name }}
                                            @elseif($beneficiary->type == BENEFICIARY_PAYPAL)
                                                {{ __('PayPal') }}: {{ $beneficiary->paypal_email }} | {{ __('Name') }}: {{ $beneficiary->beneficiary_name }}
                                            @elseif($beneficiary->type == BENEFICIARY_CARD)
                                                Card: {{ $beneficiary->card_number }} | {{ __('Holder') }}: {{ $beneficiary->card_holder_name }} | {{ __('Name') }}: {{ $beneficiary->beneficiary_name }}
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-0 pt-15">

                            <button type="submit" class="flipBtn sf-flipBtn-primary">
                                <div class="d-none h-25 spinner-border w-25" role="status">
                                    <span class="visually-hidden"></span>
                                </div>
                                {{ __('Make Withdraw') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>
    <!--Withdrawal Modal End-->

    <!-- Add Beneficiary Modal -->
     <div class="modal fade" id="add-modal" aria-hidden="true" tabindex="-1">
         <div class="modal-dialog modal-lg modal-dialog-centered">
             <div class="modal-content border-0 bd-ra-4 p-sm-25 p-15">
                 <div
                     class="bd-b-one bd-c-stroke pb-20 mb-20 d-flex align-items-center flex-wrap justify-content-between g-10">
                     <h2 class="fs-18 fw-600 lh-22 text-title-text">{{ __('Add Beneficiary') }}</h2>
                     <div class="mClose">
                         <button type="button"
                             class="bd-one bd-c-stroke rounded-circle w-25 h-25 bg-transparent text-para-text"
                             data-bs-dismiss="modal" aria-label="Close">
                             <i class="fa-solid fa-times"></i>
                         </button>
                     </div>
                 </div>

                     <form class="reset" id="beneficiaryForm" action="{{ route('affiliate.wallet.save.my-beneficiary') }}"
                         method="post">
                         @csrf
                         <div class="row rg-20 mb-20">
                             <div class="col-md-6">
                                 <label for="beneficiary_name" class="zForm-label-alt">{{ __('Beneficiary Name') }}
                                     <span class="text-danger">*</span>
                                 </label>
                                 <input type="text" name="beneficiary_name" id="beneficiary_name"
                                     placeholder="{{ __('Beneficiary Name') }}" class="form-control zForm-control-alt"
                                     required>
                             </div>

                             <div class="col-md-6">
                                 <label for="type" class="zForm-label-alt">{{ __('Type') }}
                                     <span class="text-danger">*</span>
                                 </label>
                                 <select name="type" id="type" class="form-select" required>
                                     <option value="">{{ __('Select Type') }}</option>
                                     <option value="{{ BENEFICIARY_CARD }}">{{ getBeneficiaryName(BENEFICIARY_CARD) }}
                                     </option>
                                     <option value="{{ BENEFICIARY_BANK }}">{{ getBeneficiaryName(BENEFICIARY_BANK) }}
                                     </option>
                                     <option value="{{ BENEFICIARY_PAYPAL }}">{{ getBeneficiaryName(BENEFICIARY_PAYPAL) }}
                                     </option>
                                 </select>
                             </div>

                             <!-- Card Fields -->
                             <div class="benificary-type-block d-none" id="beneficiary-{{ BENEFICIARY_CARD }}">
                                 <div class="col-md-6">
                                     <label for="card_number" class="zForm-label-alt">{{ __('Card Number') }} <span
                                             class="text-danger">*</span></label>
                                     <input type="text" name="card_number" id="card_number"
                                         placeholder="{{ __('1245 2154 2154 215') }}"
                                         class="form-control zForm-control-alt">
                                 </div>

                                 <div class="col-md-6">
                                     <label for="card_holder_name" class="zForm-label-alt">{{ __('Card Holder Name') }}
                                         <span class="text-danger">*</span></label>
                                     <input type="text" name="card_holder_name" id="card_holder_name"
                                         placeholder="{{ __('Your name') }}" class="form-control zForm-control-alt">
                                 </div>

                                 <div class="col-md-6">
                                     <label for="expire_month" class="zForm-label-alt">{{ __('Expire Month') }} <span
                                             class="text-danger">*</span></label>
                                     <select name="expire_month" id="expire_month" class="form-select">
                                         <option value="">{{ __('Select Month') }}</option>
                                         @foreach (month() as $key => $monthName)
                                             <option value="{{ $key }}">{{ __($monthName) }}</option>
                                         @endforeach
                                     </select>
                                 </div>

                                 <div class="col-md-6">
                                     <label for="expire_year" class="zForm-label-alt">{{ __('Expire Year') }} <span
                                             class="text-danger">*</span></label>
                                     <select name="expire_year" id="expire_year" class="form-select">
                                         <option value="">{{ __('Select Year') }}</option>
                                         @for ($year = \Carbon\Carbon::now()->format('Y'); $year < \Carbon\Carbon::now()->addYear(20)->format('Y'); $year++)
                                             <option value="{{ $year }}">{{ $year }}</option>
                                         @endfor
                                     </select>
                                 </div>
                             </div>

                             <!-- Bank Fields -->
                             <div class="benificary-type-block d-none" id="beneficiary-{{ BENEFICIARY_BANK }}">
                                 <div class="col-md-6">
                                     <label for="bank_name" class="zForm-label-alt">{{ __('Bank Name') }}<span
                                             class="text-danger">*</span></label>
                                     <input type="text" name="bank_name" id="bank_name"
                                         class="form-control zForm-control-alt" placeholder="{{ __('EX. Switch Bank') }}">
                                 </div>

                                 <div class="col-md-6">
                                     <label for="bank_account_name" class="zForm-label-alt">{{ __('Account Name') }}<span
                                             class="text-danger">*</span></label>
                                     <input type="text" class="form-control zForm-control-alt" name="bank_account_name"
                                         id="bank_account_name" placeholder="{{ __('Mr. XYZ') }}">
                                 </div>

                                 <div class="col-md-6">
                                     <label for="bank_account_number"
                                         class="zForm-label-alt">{{ __('Account Number') }}<span
                                             class="text-danger">*</span></label>
                                     <input type="text" class="form-control zForm-control-alt"
                                         name="bank_account_number" id="bank_account_number"
                                         placeholder="{{ __('0000000000') }}">
                                 </div>

                                 <div class="col-md-6">
                                     <label for="bank_routing_number"
                                         class="zForm-label-alt">{{ __('Routing Number') }}<span
                                             class="text-danger">*</span></label>
                                     <input type="text" class="form-control zForm-control-alt"
                                         name="bank_routing_number" id="bank_routing_number"
                                         placeholder="{{ __('Ex. 546484') }}">
                                 </div>
                             </div>

                             <!-- PayPal Fields -->
                             <div class="benificary-type-block d-none" id="beneficiary-{{ BENEFICIARY_PAYPAL }}">
                                 <div class="col-md-12">
                                     <label for="paypal_email" class="zForm-label-alt">{{ __('PayPal Email') }}<span
                                             class="text-danger">*</span></label>
                                     <input type="email" class="form-control zForm-control-alt" name="paypal_email"
                                         id="paypal_email" placeholder="EX. example@email.com">
                                 </div>
                             </div>
                         </div>
                         <div class="bd-c-stroke-2 justify-content-between align-items-center text-end pt-15">
                             <button type="submit" class="flipBtn sf-flipBtn-primary flex-shrink-0">
                                 <div class="d-none h-25 spinner-border w-25" role="status">
                                     <span class="visually-hidden"></span>
                                 </div>
                                 {{ __('Add Beneficiary') }}
                             </button>
                         </div>
                     </form>
             </div>
         </div>
     </div>

     <!-- Status Change Modal -->
     <div class="modal fade" id="status-modal" aria-hidden="true" tabindex="-1">
         <div class="modal-dialog modal-lg modal-dialog-centered">
             <div class="modal-content border-0 bd-ra-4 p-sm-25 p-15">
                 <div
                     class="bd-b-one bd-c-stroke pb-20 mb-20 d-flex align-items-center flex-wrap justify-content-between g-10">
                     <h2 class="fs-18 fw-600 lh-22 text-title-text">{{ __('Change Beneficiary Status') }}</h2>
                     <div class="mClose">
                         <button type="button"
                             class="bd-one bd-c-stroke rounded-circle w-25 h-25 bg-transparent text-para-text"
                             data-bs-dismiss="modal" aria-label="Close">
                             <i class="fa-solid fa-times"></i>
                         </button>
                     </div>
                 </div>
                 <form id="status-form" action="" method="POST">
                     @csrf
                     <div class="row rg-20 mb-20">
                         <div class="col-md-12">
                             <label for="status-select" class="zForm-label-alt">{{ __('Status') }}</label>
                             <select name="status" id="status-select" class="form-select zForm-control-alt">
                                 <option value="1">{{ __('Active') }}</option>
                                 <option value="0">{{ __('Deactivate') }}</option>
                             </select>
                         </div>
                     </div>
                     <div class="bd-c-stroke-2 justify-content-center align-items-center text-center pt-15">
                         <button type="button" class="flipBtn sf-flipBtn-secondary me-2" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                         <button type="submit" class="flipBtn sf-flipBtn-primary">{{ __('Update') }}</button>
                     </div>
                 </form>
             </div>
         </div>
     </div>
 @endsection

@push('head')
    <!-- DataTables Buttons extension CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
@endpush

@push('script')
    <!-- DataTables and Buttons extension JS -->
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script>
        (function($) {
            "use strict";


            const sharedConfig = {
                processing: true,
                serverSide: true,
                responsive: true,
                pageLength: 25,
                ordering: false,
                autoWidth: false,
                language: {
                    paginate: {
                        previous: "<i class='fa-solid fa-angles-left'></i>",
                        next: "<i class='fa-solid fa-angles-right'></i>",
                    },
                },
                dom: '<>tr<"tableBottom"<"row align-items-center"<"col-sm-6"<"tableInfo"i>><"col-sm-6"<"tablePagi"p>>>><"clear">',
                buttons: ['copy', 'excel', 'pdf'].map(type => ({
                    extend: type,
                    className: "theme-btn theme-button1 default-hover-btn"
                }))
            };


            var myEarningsTable = $('#my-earnings').DataTable({
                pageLength: 25,
                ordering: false,
                serverSide: true,
                processing: true,
                searching: true,
                responsive: true,
                ajax: "{{ route('affiliate.wallet.transaction-history') }}",
                language: {
                    paginate: {
                        previous: "<i class='fa-solid fa-angles-left'></i>",
                        next: "<i class='fa-solid fa-angles-right'></i>",
                    },
                },
                dom: '<>tr<"tableBottom"<"row align-items-center"<"col-sm-6"<"tableInfo"i>><"col-sm-6"<"tablePagi"p>>>><"clear">',
                columns: [{
                        data: "date",
                        name: "created_at"
                    },
                    {
                        data: "tnxId",
                        name: "tnxId"
                    },
                    {
                        data: "purpose",
                        name: "purpose"
                    },
                    {
                        data: "type",
                        name: "type"
                    },
                    {
                        data: "commission_percentage",
                        name: "commission_percentage"
                    },
                    {
                        data: "amount",
                        name: "amount"
                    }
                ]
            });


            var withdrawalTable = $('#withdrawalHistory').DataTable({
                pageLength: 25,
                ordering: false,
                serverSide: true,
                processing: true,
                searching: true,
                responsive: true,
                ajax: "{{ route('affiliate.wallet.withdrawal-history') }}",
                language: {
                    paginate: {
                        previous: "<i class='fa-solid fa-angles-left'></i>",
                        next: "<i class='fa-solid fa-angles-right'></i>",
                    },
                },
                dom: '<>tr<"tableBottom"<"row align-items-center"<"col-sm-6"<"tableInfo"i>><"col-sm-6"<"tablePagi"p>>>><"clear">',
                columns: [{
                        data: "date",
                        name: "created_at"
                    },
                    {
                        data: "transection_id",
                        name: "transection_id"
                    },
                    {
                        data: "note",
                        name: "note"
                    },
                    {
                        data: "gateway",
                        name: "gateway"
                    },
                    {
                        data: "amount",
                        name: "amount"
                    },
                    {
                        data: "status",
                        name: "status"
                    }
                ]
            });

            var beneficiaryTable = $('#customers-table').DataTable({
                processing: true,
                serverSide: true,
                ordering: false,
                searching: true,
                ajax: {
                    url: "{{ route('affiliate.wallet.beneficiary-history') }}",
                    data: { _token: "{{ csrf_token() }}" }
                },
                columns: [{
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'details',
                        name: 'details',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ],
                dom: '<>tr<"tableBottom"<"row align-items-center"<"col-sm-6"<"tableInfo"i>><"col-sm-6"<"tablePagi"p>>>><"clear">',
                language: {
                    paginate: {
                        previous: "<i class='fa-solid fa-angles-left'></i>",
                        next: "<i class='fa-solid fa-angles-right'></i>",
                    },
                }
            });

            $("#wallet_search_key").on("keyup", function() {
                myEarningsTable.search(this.value).draw();
                withdrawalTable.search(this.value).draw();
                beneficiaryTable.search(this.value).draw();
            });


            $(document).on('change', '.status', function() {
                $(this).closest('form').submit();
            });


            $(document).on('change', 'select[name="type"]', function() {
                let val = $(this).val();
                $('.benificary-type-block').addClass('d-none');
                if (val) {
                    $('#beneficiary-' + val).removeClass('d-none');
                }
            });


            $(document).on('input', 'input[name="card_number"]', function() {
                let value = $(this).val().replace(/\D/g, '');
                let formattedValue = value.replace(/(\d{4})(?=\d)/g, '$1 ');
                if (formattedValue !== $(this).val()) {
                    $(this).val(formattedValue);
                }
            });


            $(document).on('submit', '#beneficiaryForm', function(e) {
                e.preventDefault();

                var form = $(this);
                var submitBtn = form.find('button[type="submit"]');
                var originalText = submitBtn.html();

                submitBtn.prop('disabled', true);
                submitBtn.html(
                    '<div class="spinner-border spinner-border-sm" role="status"></div> Loading...');

                $.ajax({
                    type: 'POST',
                    url: form.attr('action'),
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            toastr.success('', response.message);
                            $('#add-modal').modal('toggle');
                            form[0].reset();
                            beneficiaryTable.ajax.reload();
                        } else {
                            toastr.error('', response.message || 'Something went wrong');
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;
                            $.each(errors, function(field, messages) {
                                toastr.error('', messages[0]);
                            });
                        } else {
                            toastr.error('', 'Something went wrong. Please try again.');
                        }
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false);
                        submitBtn.html(originalText);
                    }
                });
            });

            window.changeBeneficiaryStatusModal = function(uuid, status) {
                $('#status-select').val(status);
                $('#status-form').attr('action', '{{ url("affiliate/wallet/status-change-beneficiary") }}/' + uuid);
                $('#status-modal').modal('show');
            };

        })(jQuery);
    </script>
@endpush
