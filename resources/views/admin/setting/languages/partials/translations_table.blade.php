<div class="table-responsive zTable-responsive">
    <table class="table zTable zTable-translate">
        <thead>
        <tr>
        <tr>
            <th class="min-w-160">
                <div>{{ __('Key') }}</div>
            </th>
            <th class="min-w-160">
                <div>{{ __('Value') }}</div>
            </th>
            <th class="text-end w-28">
                <div>{{ __('Action') }}</div>
            </th>
        </tr>
        </thead>
        <tbody id="append">
        @forelse ($translators as $key => $value)
            <tr>
                <td>
                    <textarea type="text" class="key form-control" readonly required>{!! $key !!}</textarea>
                </td>
                <td>
                    <input type="hidden" value="0" class="is_new">
                    <textarea type="text" class="val form-control" required>{!! $value !!}</textarea>
                </td>
                <td class="text-end">
                    <button type="button" class="updateLangItem flipBtn sf-flipBtn-primary">{{ __('Update') }}</button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="3" class="text-center">
                    {{__('No data found')}}
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>
</div>

@php
    $totalPages = ceil($total / $perPage);
    $start = max(1, $page - 2); // show 2 pages before current
    $end = min($totalPages, $page + 2); // show 2 pages after current
@endphp

@if($totalPages > 1)
    <div class="pt-20 pt-sm-45">
        <ul class="zPaginatation-list">
            {{-- Prev Button --}}
            <li>
                <button
                   class="item {{ $page == 1 ? 'disabled' : 'ajax-page' }}"
                   data-page="{{ $page > 1 ? $page - 1 : '' }}">
                    <i class="fa-solid fa-angle-left"></i>
                </button>
            </li>

            {{-- First Page + Dots --}}
            @if($start > 1)
                <li><button class="item ajax-page" data-page="1">1</button></li>
                @if($start > 2)
                    <li><button class="item disabled">...</button></li>
                @endif
            @endif

            {{-- Page Numbers --}}
            @for($p = $start; $p <= $end; $p++)
                <li>
                    <button
                       class="item {{ $page == $p ? 'active' : 'ajax-page' }}"
                       data-page="{{ $p }}">
                        {{ $p }}
                    </button>
                </li>
            @endfor

            {{-- Last Page + Dots --}}
            @if($end < $totalPages)
                @if($end < $totalPages - 1)
                    <li><button class="item disabled">...</button></li>
                @endif
                <li><button class="item ajax-page" data-page="{{ $totalPages }}">{{ $totalPages }}</button></li>
            @endif

            {{-- Next Button --}}
            <li>
                <button class="item {{ $page == $totalPages ? 'disabled' : 'ajax-page' }}"
                   data-page="{{ $page < $totalPages ? $page + 1 : '' }}">
                    <i class="fa-solid fa-angle-right"></i>
                </button>
            </li>
        </ul>
    </div>
@endif
