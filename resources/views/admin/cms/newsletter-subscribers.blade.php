@extends('layouts.app')
@push('title'){{ __('Newsletter subscribers') }}@endpush
@section('content')
<main class="p-sm-30 p-15">
    <header class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div><h1 class="fs-24 fw-700">{{ __('Newsletter subscribers') }}</h1><p class="text-muted mb-0">{{ __('People who opted in using the website newsletter form.') }}</p></div>
        <a class="btn btn-outline-primary" href="{{ route('admin.cms-settings.public-site.edit') }}">{{ __('Website editor') }}</a>
    </header>
    @include('partials.list-criteria')
    <div class="table-responsive bg-white rounded p-3">
        <table class="table align-middle mb-0">
            <thead><tr><th>{{ __('Email') }}</th><th>{{ __('Status') }}</th><th>{{ __('Consent recorded') }}</th><th>{{ __('Edit subscription') }}</th><th>{{ __('Delete') }}</th></tr></thead>
            <tbody>
                @forelse($subscribers as $subscriber)
                <tr>
                    <td>{{ $subscriber->email }}</td>
                    <td><span class="zBadge {{ $subscriber->status === 'subscribed' ? 'zBadge-active' : 'zBadge-deactive' }}">{{ __(ucfirst($subscriber->status)) }}</span></td>
                    <td>{{ $subscriber->consent_at?->format('M j, Y H:i') }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.cms-settings.public-site.subscribers.update', $subscriber) }}" class="d-flex flex-wrap align-items-center gap-2">
                            @csrf @method('PATCH')
                            <input name="email" type="email" required maxlength="190" class="form-control" style="max-width:260px" value="{{ $subscriber->email }}" aria-label="{{ __('Subscriber email') }}">
                            <select name="status" class="form-select" style="max-width:170px" aria-label="{{ __('Subscription status') }}"><option value="subscribed" @selected($subscriber->status === 'subscribed')>{{ __('Subscribed') }}</option><option value="unsubscribed" @selected($subscriber->status === 'unsubscribed')>{{ __('Unsubscribed') }}</option></select>
                            <button class="btn btn-primary">{{ __('Save') }}</button>
                        </form>
                    </td>
                    <td><form method="POST" action="{{ route('admin.cms-settings.public-site.subscribers.destroy', $subscriber) }}" onsubmit="return confirm(@js(__('Delete this subscriber?')))">@csrf @method('DELETE')<button type="submit" class="genz-row-action genz-row-action--delete" title="{{ __('Delete') }}" aria-label="{{ __('Delete') }}">@include('partials.icons.delete')</button></form></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-4">{{ __('No subscribers match these criteria.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">{{ $subscribers->links() }}</div>
</main>
@endsection
