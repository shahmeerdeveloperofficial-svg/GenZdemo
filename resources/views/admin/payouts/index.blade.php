@extends('layouts.app')

@push('title')
    {{ $pageTitle }}
@endpush

@section('content')
<div class="p-sm-30 p-15 pt-54">
    <div class="p-sm-25 p-15 bd-one bd-c-stroke bd-ra-5 bg-white">
        <div class="zTab-wrap">
            <ul class="nav nav-tabs zTab-reset zTab-one" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active payoutStatusTab" data-status="all" id="all-tab"
                            data-bs-toggle="tab" data-bs-target="#all-tab-pane" type="button" role="tab"
                            aria-controls="all-tab-pane" aria-selected="true">
                        {{ __('All') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link payoutStatusTab" data-status="pending" id="pending-tab" data-bs-toggle="tab"
                            data-bs-target="#pending-tab-pane" type="button" role="tab" aria-controls="pending-tab-pane"
                            aria-selected="false">
                        {{ __('Pending') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link payoutStatusTab" data-status="completed" id="completed-tab"
                            data-bs-toggle="tab" data-bs-target="#completed-tab-pane" type="button" role="tab"
                            aria-controls="completed-tab-pane" aria-selected="false">
                        {{ __('Paid') }}
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link payoutStatusTab" data-status="rejected" id="rejected-tab"
                            data-bs-toggle="tab" data-bs-target="#rejected-tab-pane" type="button" role="tab"
                            aria-controls="rejected-tab-pane" aria-selected="false">
                        {{ __('Rejected') }}
                    </button>
                </li>
            </ul>
        </div>

        <!-- Filter Section -->
        <div class="pb-30">
            <div class="row rg-24 justify-content-center align-items-xl-end">
                <div class="col-xl-9">
                    <div class="tableFilter-wrap tableFilter-wrap-alt">
                        <div class="item">
                            {{-- <label for="search-key" class="zForm-label-alt">{{__('Search')}}</label> --}}
                            <input type="text" class="form-control zForm-control-alt" id="search-key"
                                    placeholder="{{__('Search here')}}"/>
                        </div>
                        <div class="filterBtn">
                            <button class="flipBtn-icon sf-flipBtn-primary" id="filterButton">
                                <div class="item">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="20" viewBox="0 0 21 20" fill="none">
                                        <ellipse cx="9.26461" cy="8.99077" rx="6.7712" ry="6.69878" stroke="#121D35" stroke-width="1.5" stroke-linecap="square"/>
                                        <path d="M13.8879 13.9236L17.7235 17.7083" stroke="#121D35" stroke-width="1.5" stroke-linecap="square"/>
                                    </svg>
                                    <span>{{__('Filter')}}</span>
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="all-tab-pane" role="tabpanel" aria-labelledby="all-tab">
                @include('admin.payouts.table', ['status' => 'all'])
            </div>
            <div class="tab-pane fade" id="pending-tab-pane" role="tabpanel" aria-labelledby="pending-tab">
                @include('admin.payouts.table', ['status' => 'pending'])
            </div>
            <div class="tab-pane fade" id="completed-tab-pane" role="tabpanel" aria-labelledby="completed-tab">
                @include('admin.payouts.table', ['status' => 'completed'])
            </div>
            <div class="tab-pane fade" id="rejected-tab-pane" role="tabpanel" aria-labelledby="rejected-tab">
                @include('admin.payouts.table', ['status' => 'rejected'])
            </div>
        </div>
    </div>
</div>

<!-- Modal section start -->
<div class="modal fade" id="payout-action-modal" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 bd-ra-4 p-sm-25 p-15" style="max-height: 80vh; overflow-y: auto;">

        </div>
    </div>
</div>

<div class="modal fade" id="payout-view-modal" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 bd-ra-4 p-sm-25 p-15 viewInvoiceModal" style="max-height: 80vh; overflow-y: auto;">

        </div>
    </div>
</div>

<input type="hidden" id="payoutsRoute" value="{{ route('admin.payouts.index') }}">
<input type="hidden" id="getCurrencyByGatewayRoute" value="{{ route('gateway-currency') }}">

@endsection

@push('script')
<script>
(function ($) {
    "use strict";

    $(document).ready(function () {
        // Initialize active table (all) on page load
        allPayoutDataTable('all');

        $('#filterButton').on('click', function () {
            // Reload all initialized tables
            const statuses = ['all', 'pending', 'completed', 'rejected'];
            statuses.forEach(function(status) {
                const tableId = '#payoutDataTable' + status.charAt(0).toUpperCase() + status.slice(1);
                if ($.fn.DataTable.isDataTable(tableId)) {
                    $(tableId).DataTable().ajax.reload();
                }
            });
        });

        // Search on keyup
        $('#search-key').on('keyup', function () {
            // Reload all initialized tables
            const statuses = ['all', 'pending', 'completed', 'rejected'];
            statuses.forEach(function(status) {
                const tableId = '#payoutDataTable' + status.charAt(0).toUpperCase() + status.slice(1);
                if ($.fn.DataTable.isDataTable(tableId)) {
                    $(tableId).DataTable().ajax.reload();
                }
            });
        });
    });

    // Action button handler
    $(document).on('click', '.action-btn', function() {
        const url = $(this).data('url');
        getEditModal(url, '#payout-action-modal');
    });

    $(document).on('shown.bs.tab', '.payoutStatusTab', function (e) {
        var status = $(this).data('status');
        var tableId = '#payoutDataTable' + status.charAt(0).toUpperCase() + status.slice(1);

        // Initialize table if not already done
        if (!$.fn.DataTable.isDataTable(tableId)) {
            allPayoutDataTable(status);
        } else {
            // Reload if already initialized
            $(tableId).DataTable().ajax.reload();
        }
    });

    function allPayoutDataTable(status) {
        var tableId = "#payoutDataTable" + status.charAt(0).toUpperCase() + status.slice(1);
        var dataTableColumns = [
            {"data": "uuid"},
            {"data": "user_name"},
            {"data": "email"},
            {"data": "amount"},
            {"data": "gateway"},
            {"data": "transaction_date"}
        ];

        if (status === 'all') {
            dataTableColumns.splice(1, 0, {"data": "transaction_id"});
            dataTableColumns.push({"data": "status"});
            dataTableColumns.push({"data": "action", responsivePriority: 2});
        } else if (status === 'pending') {
            dataTableColumns.push({"data": "action", responsivePriority: 2});
        } else if (status === 'completed') {
            dataTableColumns.splice(1, 0, {"data": "transaction_id"});
            dataTableColumns.push({"data": "action", responsivePriority: 2});
        } else if (status === 'rejected') {
            dataTableColumns.splice(1, 0, {"data": "transaction_id"});
            dataTableColumns.push({"data": "narration"});
        }

        $(tableId).DataTable({
            pageLength: 10,
            ordering: true,
            serverSide: true,
            processing: true,
            searching: false,
            ajax: {
                url: $('#payoutsRoute').val(),
                data: function (data) {
                    data.status = status;
                    data.search_key = $('#search-key').val();
                },
                error: function (xhr) {
                    console.log('Error: ', xhr.responseText);
                    window.GenzToast.error('An error occurred while loading payouts. Please try again.');
                }
            },
            language: {
                paginate: {
                    previous: "<i class='fa-solid fa-angles-left'></i>",
                    next: "<i class='fa-solid fa-angles-right'></i>",
                },
            },
            dom: '<>tr<"tableBottom"<"row align-items-center"<"col-sm-6"<"tableInfo"i>><"col-sm-6"<"tablePagi"p>>>><"clear">',
            columns: dataTableColumns,
            stateSave: true,
            "bDestroy": true
        });
    }

})(jQuery);
</script>
@endpush

