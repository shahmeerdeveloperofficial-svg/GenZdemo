@extends('layouts.app')
@push('title'){{ __('Lead CRM') }}@endpush
@section('content')
<div class="p-sm-30 p-15">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><p class="text-muted mb-1">{{ __('Consultancy pipeline') }}</p><h1 class="fs-24 fw-700 mb-0">{{ __('Leads') }}</h1></div>
        <div class="d-flex gap-2"><a class="genz-button-light" href="{{ route($routePrefix . '.imports.create') }}">{{ __('Import CSV') }}</a><a class="genz-button-primary" href="{{ route($routePrefix . '.create') }}">{{ __('Add lead') }}</a></div>
    </div>
    <div class="genz-crm-pipeline mb-4">
        @foreach (['new' => 'New', 'contacted' => 'Contacted', 'qualified' => 'Qualified', 'consultation' => 'Consultation', 'converted' => 'Converted', 'lost' => 'Lost'] as $key => $label)
            <a class="genz-kpi-card" href="{{ route($routePrefix . '.index', ['status' => $key]) }}"><span>{{ __($label) }}</span><strong>{{ $counts[$key] ?? 0 }}</strong></a>
        @endforeach
    </div>
    <form method="GET" class="genz-contact-card genz-crm-filters genz-lead-criteria mb-4">
        <div><label class="form-label" for="lead-search">{{ __('Search name, email or phone') }}</label><input id="lead-search" name="q" value="{{ request('q') }}" class="form-control genz-field" maxlength="120"></div>
        <div><label class="form-label" for="lead-status">{{ __('Stage') }}</label><select id="lead-status" name="status" class="form-select genz-field"><option value="">{{ __('All stages') }}</option>@foreach (\App\Models\CrmLead::STATUSES as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ __(ucfirst($status)) }}</option>@endforeach</select></div>
        <div><label class="form-label" for="lead-source">{{ __('Source') }}</label><select id="lead-source" name="source" class="form-select genz-field"><option value="">{{ __('All sources') }}</option>@foreach ($sources as $source)<option value="{{ $source }}" @selected(request('source') === $source)>{{ __(\Illuminate\Support\Str::headline($source)) }}</option>@endforeach</select></div>
        <div><label class="form-label" for="lead-branch">{{ __('Branch') }}</label><select id="lead-branch" name="branch" class="form-select genz-field"><option value="">{{ __('All branches') }}</option>@foreach(\App\Support\ListCriteria::branchOptions() as $value => $label)<option value="{{ $value }}" @selected(request('branch') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div><label class="form-label" for="lead-owner">{{ __('Owner') }}</label><select id="lead-owner" name="assigned_to" class="form-select genz-field"><option value="">{{ __('All owners') }}</option>@foreach($owners as $owner)<option value="{{ $owner->id }}" @selected((string) request('assigned_to') === (string) $owner->id)>{{ $owner->name }}</option>@endforeach</select></div>
        <div><label class="form-label" for="lead-period">{{ __('Created') }}</label><select id="lead-period" name="period" class="form-select genz-field"><option value="">{{ __('All dates') }}</option>@foreach(\App\Support\ListCriteria::periods() as $value => $label)<option value="{{ $value }}" @selected((string) request('period') === (string) $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="genz-crm-filter-actions"><button class="genz-button-primary" type="submit">{{ __('Filter') }}</button><a class="genz-button-light" href="{{ route($routePrefix . '.index') }}">{{ __('Reset') }}</a></div>
    </form>
    <div class="genz-crm-list">
    <form id="crm-bulk-assign" method="POST" action="{{ route($routePrefix . '.bulk_assign') }}">@csrf
    <div class="d-flex flex-wrap align-items-center gap-2 mb-3"><label class="visually-hidden" for="bulk-owner">{{ __('Assign selected leads') }}</label><select id="bulk-owner" name="assigned_to" class="form-select genz-field" style="max-width:260px"><option value="">{{ __('Unassign selected') }}</option>@foreach ($owners as $owner)<option value="{{ $owner->id }}">{{ $owner->name }}</option>@endforeach</select><button class="genz-button-light" type="submit">{{ __('Assign selected') }}</button><span class="text-muted">{{ __('Select up to 100 leads on this page.') }}</span></div>
    </form>
    <div class="table-responsive genz-contact-card p-0"><table class="table align-middle mb-0"><thead><tr><th><input type="checkbox" aria-label="{{ __('Select all leads on this page') }}" onclick="document.querySelectorAll('.crm-lead-checkbox').forEach((box) => box.checked = this.checked)"></th><th>{{ __('Lead') }}</th><th>{{ __('Contact') }}</th><th>{{ __('Stage') }}</th><th>{{ __('Source') }}</th><th>{{ __('Owner') }}</th><th>{{ __('Created') }}</th><th>{{ __('Actions') }}</th></tr></thead><tbody>
    @forelse ($leads as $lead)
        <tr><td><input class="crm-lead-checkbox" type="checkbox" form="crm-bulk-assign" name="lead_ids[]" value="{{ $lead->id }}" aria-label="{{ __('Select :name', ['name' => $lead->first_name]) }}"></td><td><a href="{{ route($routePrefix . '.show', $lead) }}"><strong>{{ $lead->first_name }} {{ $lead->last_name }}</strong></a></td><td>{{ $lead->email }}<br>{{ $lead->phone }}</td><td><span class="badge bg-light text-dark">{{ __(ucfirst($lead->status)) }}</span></td><td>{{ __(\Illuminate\Support\Str::headline($lead->source)) }}</td><td>{{ $lead->owner?->name ?? __('Unassigned') }}</td><td>{{ $lead->created_at->format('M j, Y') }}</td><td><div class="d-flex justify-content-end gap-2"><a class="genz-row-action genz-row-action--edit" href="{{ route($routePrefix . '.show', $lead) }}" title="{{ __('Edit lead') }}" aria-label="{{ __('Edit lead') }}"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i></a><form method="POST" action="{{ route($routePrefix . '.destroy', $lead) }}" onsubmit="return confirm('{{ __('Move this lead to deleted records?') }}')">@csrf @method('DELETE')<button class="genz-row-action genz-row-action--delete" type="submit" title="{{ __('Delete lead') }}" aria-label="{{ __('Delete lead') }}"><i class="fa-solid fa-trash" aria-hidden="true"></i></button></form></div></td></tr>
    @empty
        <tr><td colspan="8" class="text-center py-5"><strong>{{ __('No leads match this view yet.') }}</strong><p class="mb-0 text-muted">{{ __('Website enquiries will appear here automatically, or add a lead manually.') }}</p></td></tr>
    @endforelse
    </tbody></table></div>
    </div><div class="mt-4">{{ $leads->links() }}</div>
</div>
@endsection
