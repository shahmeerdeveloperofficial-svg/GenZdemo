@extends('layouts.app')

@push('title'){{ __('Analytics & reports') }}@endpush

@push('style')
<style>
    .analytics-page{--an-ink:#123b78;--an-muted:#7183a0;--an-line:#e5edf7;--an-blue:#1789bd;color:var(--an-ink)}
    .analytics-heading{display:flex;align-items:flex-end;justify-content:space-between;gap:18px;margin-bottom:22px}
    .analytics-heading h1{margin:0;font-size:26px;font-weight:750;letter-spacing:-.45px;color:#123b78}
    .analytics-heading p{margin:6px 0 0;color:var(--an-muted)}
    .analytics-range{display:flex;align-items:flex-end;gap:12px;flex-wrap:wrap;padding:18px 20px;background:#fff;border:1px solid var(--an-line);border-radius:16px;box-shadow:0 7px 24px rgba(29,68,121,.045)}
    .analytics-range__field{min-width:180px}.analytics-range label{display:block;margin:0 0 6px;font-size:13px;font-weight:650;color:#536985}
    .analytics-range .form-control{height:44px;border-color:#dce6f2;border-radius:10px;color:#24466f}
    .analytics-range .btn{height:44px;padding:0 19px;border-radius:10px;font-weight:650}
    .analytics-note{display:flex;align-items:center;gap:9px;margin:13px 2px 22px;color:#7385a0;font-size:13px}
    .analytics-note i{color:#1689ba}
    .analytics-kpis{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px;margin-bottom:20px}
    .analytics-kpi,.analytics-panel{background:#fff;border:1px solid var(--an-line);border-radius:16px;box-shadow:0 8px 26px rgba(29,68,121,.05)}
    .analytics-kpi{position:relative;min-height:126px;padding:19px 20px;overflow:hidden}
    .analytics-kpi__label{margin:0 0 10px;color:#7183a0;font-size:13px;font-weight:600}
    .analytics-kpi__value{margin:0;color:#103b79;font-size:29px;line-height:1.1;font-weight:750;letter-spacing:-.6px}
    .analytics-kpi__icon{position:absolute;right:18px;top:18px;display:grid;place-items:center;width:42px;height:42px;border-radius:13px;background:#edf6ff;color:#2786d7;font-size:17px}
    .analytics-panel{padding:20px 22px;margin-bottom:18px}
    .analytics-panel__heading{display:flex;align-items:flex-start;justify-content:space-between;gap:14px;margin-bottom:18px}
    .analytics-panel__title{display:flex;align-items:center;gap:11px;margin:0;color:#123b78;font-size:17px;font-weight:700}
    .analytics-panel__icon{display:grid;place-items:center;flex:0 0 36px;width:36px;height:36px;border-radius:11px;background:#eaf6fc;color:#1287b9}
    .analytics-panel__description{margin:5px 0 0 47px;color:var(--an-muted);font-size:12px}
    .analytics-count{padding:5px 10px;border-radius:20px;background:#f0f5fb;color:#58708f;font-size:12px;font-weight:650;white-space:nowrap}
    .analytics-bars{display:grid;gap:15px}
    .analytics-bar__meta{display:flex;justify-content:space-between;gap:12px;margin-bottom:7px;color:#425b7b;font-size:13px}
    .analytics-bar__meta strong{color:#143d78}
    .analytics-bar__track{height:9px;overflow:hidden;border-radius:20px;background:#edf2f8}
    .analytics-bar__fill{height:100%;min-width:3px;border-radius:20px;background:linear-gradient(90deg,#1689bd,#4aa9e8)}
    .analytics-table{margin:0;color:#385373;vertical-align:middle}
    .analytics-table thead th{padding:11px 13px;border:0;background:#f3f7fc;color:#627894;font-size:11px;font-weight:700;letter-spacing:.045em;text-transform:uppercase}
    .analytics-table thead th:first-child{border-radius:9px 0 0 9px}.analytics-table thead th:last-child{border-radius:0 9px 9px 0}
    .analytics-table tbody td{padding:13px;border-color:#edf1f6;font-size:13px}
    .analytics-table tbody tr:last-child td{border-bottom:0}
    .analytics-badge{display:inline-flex;align-items:center;padding:5px 9px;border-radius:20px;background:#edf6ff;color:#226fae;font-size:11px;font-weight:700}
    .analytics-empty{display:flex;align-items:center;gap:13px;padding:17px;border:1px dashed #dce6f2;border-radius:12px;background:#fbfdff;color:#7183a0;font-size:13px}
    .analytics-empty i{font-size:17px;color:#92a8c4}
    .analytics-payment-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
    .analytics-payment{display:flex;justify-content:space-between;align-items:center;gap:14px;padding:14px 15px;border:1px solid #e9eff6;border-radius:12px;background:#fcfdff}
    .analytics-payment__type{color:#405a7b;font-size:13px;font-weight:650}.analytics-payment__currency{margin-left:6px;color:#8696ac;font-size:11px;font-weight:600}
    .analytics-payment__total{text-align:right;color:#143d78;font-weight:750;font-size:15px}.analytics-payment__count{display:block;margin-top:3px;color:#8797ad;font-size:11px;font-weight:500}
    @media(max-width:900px){.analytics-kpis{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media(max-width:600px){.analytics-heading{align-items:flex-start;flex-direction:column}.analytics-heading h1{font-size:22px}.analytics-range{align-items:stretch}.analytics-range__field{min-width:calc(50% - 6px);flex:1}.analytics-range .btn{width:100%}.analytics-panel{padding:16px}.analytics-kpis{gap:10px}.analytics-kpi{min-height:112px;padding:15px}.analytics-kpi__value{font-size:25px}.analytics-kpi__icon{top:13px;right:13px;width:35px;height:35px}.analytics-payment-list{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
@php
    $leadTotal = (int) $crmRows->sum('total');
    $applicationTotal = (int) $applicationRows->sum('total');
    $enrollmentTotal = (int) $courseRows->sum('enrollments');
    $paymentTotal = (int) $paymentRows->sum('payments');
    $maxLead = max(1, (int) $crmRows->max('total'));
    $maxApplication = max(1, (int) $applicationRows->max('total'));
    $maxEnrollment = max(1, (int) $courseRows->max('enrollments'));
@endphp

<main class="analytics-page p-sm-30 p-15">
    <header class="analytics-heading">
        <div>
            <h1>{{ __('Analytics & reports') }}</h1>
            <p>{{ __('A clear summary of activity recorded in your workspace.') }}</p>
        </div>
    </header>

    <form method="GET" action="{{ route('admin.analytics') }}" class="analytics-range">
        <div class="analytics-range__field">
            <label for="report-from">{{ __('From') }}</label>
            <input id="report-from" type="date" name="from" value="{{ $from->toDateString() }}" class="form-control">
        </div>
        <div class="analytics-range__field">
            <label for="report-to">{{ __('To') }}</label>
            <input id="report-to" type="date" name="to" value="{{ $to->toDateString() }}" class="form-control">
        </div>
        <button class="btn btn-primary" type="submit"><i class="fa-solid fa-filter me-2" aria-hidden="true"></i>{{ __('Apply filters') }}</button>
    </form>

    <p class="analytics-note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i>{{ __('All figures are based on saved records in the selected date range. Demo counters are not included.') }}</p>

    <section class="analytics-kpis" aria-label="{{ __('Report summary') }}">
        @if($canCrm)
            <article class="analytics-kpi"><span class="analytics-kpi__icon"><i class="fa-solid fa-user-plus" aria-hidden="true"></i></span><p class="analytics-kpi__label">{{ __('Leads created') }}</p><p class="analytics-kpi__value">{{ number_format($leadTotal) }}</p></article>
            <article class="analytics-kpi"><span class="analytics-kpi__icon" style="background:#f1edff;color:#765ddb"><i class="fa-solid fa-file-circle-check" aria-hidden="true"></i></span><p class="analytics-kpi__label">{{ __('Applications created') }}</p><p class="analytics-kpi__value">{{ number_format($applicationTotal) }}</p></article>
        @endif
        @if($canLms)
            <article class="analytics-kpi"><span class="analytics-kpi__icon" style="background:#e8f8f3;color:#1d9d78"><i class="fa-solid fa-book-open" aria-hidden="true"></i></span><p class="analytics-kpi__label">{{ __('LMS enrollments') }}</p><p class="analytics-kpi__value">{{ number_format($enrollmentTotal) }}</p></article>
        @endif
        @if($canFinance)
            <article class="analytics-kpi"><span class="analytics-kpi__icon" style="background:#fff3e7;color:#d8882a"><i class="fa-solid fa-receipt" aria-hidden="true"></i></span><p class="analytics-kpi__label">{{ __('Confirmed payments') }}</p><p class="analytics-kpi__value">{{ number_format($paymentTotal) }}</p></article>
        @endif
    </section>

    @if($canCrm)
        <section class="analytics-panel">
            <div class="analytics-panel__heading">
                <div><h2 class="analytics-panel__title"><span class="analytics-panel__icon"><i class="fa-solid fa-filter-circle-dollar" aria-hidden="true"></i></span>{{ __('Lead pipeline') }}</h2><p class="analytics-panel__description">{{ __('Leads grouped by their current CRM status.') }}</p></div>
                <span class="analytics-count">{{ number_format($leadTotal) }} {{ __('total') }}</span>
            </div>
            @if($crmRows->isEmpty())
                <div class="analytics-empty"><i class="fa-regular fa-folder-open" aria-hidden="true"></i>{{ __('No lead records found for this date range.') }}</div>
            @else
                <div class="analytics-bars">
                    @foreach($crmRows as $row)
                        <div><div class="analytics-bar__meta"><span>{{ __(ucfirst(str_replace('_',' ',$row->status))) }}</span><strong>{{ number_format($row->total) }}</strong></div><div class="analytics-bar__track"><div class="analytics-bar__fill" style="width:{{ min(100, ((int)$row->total / $maxLead) * 100) }}%"></div></div></div>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="analytics-panel">
            <div class="analytics-panel__heading">
                <div><h2 class="analytics-panel__title"><span class="analytics-panel__icon" style="background:#f1edff;color:#765ddb"><i class="fa-solid fa-file-lines" aria-hidden="true"></i></span>{{ __('Admissions pipeline') }}</h2><p class="analytics-panel__description">{{ __('Applications grouped by their current stage.') }}</p></div>
                <span class="analytics-count">{{ number_format($applicationTotal) }} {{ __('total') }}</span>
            </div>
            @if($applicationRows->isEmpty())
                <div class="analytics-empty"><i class="fa-regular fa-folder-open" aria-hidden="true"></i>{{ __('No application records found for this date range.') }}</div>
            @else
                <div class="analytics-bars">
                    @foreach($applicationRows as $row)
                        <div><div class="analytics-bar__meta"><span>{{ __(ucfirst(str_replace('_',' ',$row->status))) }}</span><strong>{{ number_format($row->total) }}</strong></div><div class="analytics-bar__track"><div class="analytics-bar__fill" style="width:{{ min(100, ((int)$row->total / $maxApplication) * 100) }}%;background:linear-gradient(90deg,#7866dc,#a795f3)"></div></div></div>
                    @endforeach
                </div>
            @endif
        </section>
    @endif

    @if($canLms)
        <section class="analytics-panel">
            <div class="analytics-panel__heading">
                <div><h2 class="analytics-panel__title"><span class="analytics-panel__icon" style="background:#e8f8f3;color:#1d9d78"><i class="fa-solid fa-graduation-cap" aria-hidden="true"></i></span>{{ __('LMS enrollments by course') }}</h2><p class="analytics-panel__description">{{ __('Courses with enrollments created in this period.') }}</p></div>
                <span class="analytics-count">{{ number_format($enrollmentTotal) }} {{ __('total') }}</span>
            </div>
            @if($courseRows->isEmpty())
                <div class="analytics-empty"><i class="fa-regular fa-folder-open" aria-hidden="true"></i>{{ __('No enrollment records found for this date range.') }}</div>
            @else
                <div class="table-responsive"><table class="table analytics-table"><thead><tr><th>{{ __('Course') }}</th><th>{{ __('Enrollments') }}</th><th>{{ __('Share of total') }}</th></tr></thead><tbody>
                    @foreach($courseRows as $row)
                        <tr><td class="fw-semibold">{{ $row->title }}</td><td>{{ number_format($row->enrollments) }}</td><td><div class="analytics-bar__track" style="max-width:260px"><div class="analytics-bar__fill" style="width:{{ min(100, ((int)$row->enrollments / $maxEnrollment) * 100) }}%;background:linear-gradient(90deg,#20a27c,#62c9a7)"></div></div></td></tr>
                    @endforeach
                </tbody></table></div>
            @endif
        </section>
    @endif

    @if($canFinance)
        <section class="analytics-panel">
            <div class="analytics-panel__heading">
                <div><h2 class="analytics-panel__title"><span class="analytics-panel__icon" style="background:#fff3e7;color:#d8882a"><i class="fa-solid fa-money-bill-transfer" aria-hidden="true"></i></span>{{ __('Confirmed payments') }}</h2><p class="analytics-panel__description">{{ __('Paid totals grouped by order type and currency.') }}</p></div>
                <span class="analytics-count">{{ number_format($paymentTotal) }} {{ __('payments') }}</span>
            </div>
            <div class="analytics-empty mb-3"><i class="fa-solid fa-circle-info" aria-hidden="true"></i>{{ __('Amounts are gross paid totals; refunds are not subtracted. Different currencies are shown separately.') }}</div>
            @if($paymentRows->isEmpty())
                <div class="analytics-empty"><i class="fa-regular fa-folder-open" aria-hidden="true"></i>{{ __('No confirmed payment records found for this date range.') }}</div>
            @else
                <div class="analytics-payment-list">
                    @foreach($paymentRows as $row)
                        <article class="analytics-payment"><div class="analytics-payment__type">{{ class_basename($row->paymentable_type) }}<span class="analytics-payment__currency">{{ $row->system_currency }}</span></div><div class="analytics-payment__total">{{ number_format((float)$row->gross_total, 2) }}<span class="analytics-payment__count">{{ number_format($row->payments) }} {{ __('payments') }}</span></div></article>
                    @endforeach
                </div>
            @endif
        </section>
    @endif
</main>
@endsection
