<form method="GET" action="{{ url()->current() }}" class="genz-list-criteria mb-4">
    <div class="genz-list-criteria__heading"><i class="fa-solid fa-filter" aria-hidden="true"></i><strong>{{ __('Filter records') }}</strong></div>
    <div class="genz-list-criteria__fields">
        @foreach($criteriaFields as $field)
            <div class="genz-list-criteria__field">
                <label for="criteria-{{ $field['name'] }}">{{ $field['label'] }}</label>
                @if(($field['type'] ?? '') === 'search')
                <input id="criteria-{{ $field['name'] }}" name="{{ $field['name'] }}" type="search" maxlength="120" value="{{ request($field['name']) }}" class="form-control" placeholder="{{ $field['placeholder'] ?? '' }}">
                @else
                <select id="criteria-{{ $field['name'] }}" name="{{ $field['name'] }}" class="form-select">
                    <option value="">{{ $field['all'] ?? __('All') }}</option>
                    @foreach($field['options'] as $value => $label)
                        <option value="{{ $value }}" @selected((string) request($field['name'], $field['default'] ?? '') === (string) $value)>{{ $label }}</option>
                    @endforeach
                </select>
                @endif
            </div>
        @endforeach
        <div class="genz-list-criteria__actions">
            <button type="submit" class="btn btn-primary">{{ __('Apply filters') }}</button>
            <a href="{{ url()->current() }}" class="btn btn-outline-primary">{{ __('Reset') }}</a>
        </div>
    </div>
</form>
