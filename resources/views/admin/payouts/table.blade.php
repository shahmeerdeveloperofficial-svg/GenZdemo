<table class="table zTable zTable-last-item-right" id="payoutDataTable{{ ucfirst($status) }}">
    <thead>
    <tr>
        <th><div class="text-nowrap">{{ __('UUID') }}</div></th>
        @if (strtolower($status) !== 'pending')
            <th><div class="text-nowrap">{{ __('Transaction ID') }}</div></th>
        @endif
        <th><div class="text-nowrap">{{ __('User Name') }}</div></th>
        <th><div class="text-nowrap">{{ __('Email') }}</div></th>
        <th><div class="text-nowrap">{{ __('Amount') }}</div></th>
        <th><div>{{ __('Gateway') }}</div></th>
        <th><div class="text-nowrap">{{ __('Transaction Date') }}</div></th>
        @if (strtolower($status) === 'all')
            <th><div>{{ __('Status') }}</div></th>
            <th><div>{{ __('Action') }}</div></th>
        @elseif (strtolower($status) === 'pending')
            <th><div>{{ __('Action') }}</div></th>
        @elseif (strtolower($status) === 'completed')
            <th><div>{{ __('Action') }}</div></th>
        @elseif (strtolower($status) === 'rejected')
            <th><div>{{ __('Reason') }}</div></th>
        @endif
    </tr>
    </thead>
</table>
