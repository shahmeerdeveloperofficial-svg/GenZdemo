@extends('layouts.app')
@push('title')
    {{ __('Import Consultation Requests Preview') }}
@endpush
@section('content')
    <div class="px-sm-30 p-15 bd-b-one bd-c-stroke-2 d-flex justify-content-between align-items-center">
        <h4 class="fs-18 fw-700 lh-24 text-title-text">{{__('Import Consultation Requests Preview')}}</h4>
    </div>
    <div class="p-sm-30 p-15">
        <div class="p-sm-25 p-15 bd-one bd-c-stroke bd-ra-10 bg-white">
            <div class="mb-20">
                <div class="alert alert-success">
                    <p class="mb-0"><strong>{{__('All consultation requests below have valid required fields (First Name, Last Name, Email, Mobile, Study Level) and will be imported. Invalid rows have been automatically excluded.')}}</strong></p>
                </div>
            </div>
            <form action="{{ route('admin.free_consultations.save_import') }}" method="post" id="save-leads-form">
                @csrf
                <input type="hidden" name="session_key" value="{{ $sessionKey }}">
                <div class="table-responsive">
                    <table class="table zTable">
                        <thead>
                        <tr>
                            <th>{{__('Sl')}}</th>
                            <th>{{__('First Name')}}</th>
                            <th>{{__('Last Name')}}</th>
                            <th>{{__('Email')}}</th>
                            <th>{{__('Mobile')}}</th>
                            <th>{{__('Study Level')}}</th>
                            <th>{{__('Consultation Type')}}</th>
                            <th>{{__('Fund Type')}}</th>
                            <th>{{__('Countries')}}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($leads as $index => $lead)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $lead['first_name'] ?? '-' }}</td>
                                <td>{{ $lead['last_name'] ?? '-' }}</td>
                                <td>{{ $lead['email'] ?? '-' }}</td>
                                <td>{{ $lead['mobile'] ?? '-' }}</td>
                                <td>{{ $lead['study_level_name'] ?? '-' }}</td>
                                <td>
                                    @if(!empty($lead['consultation_type_id']))
                                        {{ $lead['consultation_type_id'] == CONSULTATION_TYPE_PHYSICAL ? __('Physical') : __('Virtual') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($lead['fund_type_id']))
                                        @php
                                            $fundTypes = [
                                                FUND_TYPE_SELF => __('Self'),
                                                FUND_TYPE_PARENTS => __('Parents'),
                                                FUND_TYPE_SEEKING_SCHOLARSHIP => __('Seeking Scholarship'),
                                                FUND_TYPE_BANK_LOAN => __('Bank Loan'),
                                                FUND_TYPE_EMPLOYER_SCHOLARSHIP => __('Employer Scholarship'),
                                                FUND_TYPE_OTHER => __('Other'),
                                            ];
                                        @endphp
                                        {{ $fundTypes[$lead['fund_type_id']] ?? '-' }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!empty($lead['country_ids']))
                                        @php
                                            $countryNames = \App\Models\Country::whereIn('id', $lead['country_ids'])->pluck('name')->toArray();
                                        @endphp
                                        {{ implode(', ', $countryNames) }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="bd-c-stroke-2 justify-content-between align-items-center d-flex flex-column flex-sm-row g-15 pt-20 mt-20">
                    <div class="d-flex flex-wrap align-items-center g-15">
                        <div class="d-flex align-items-center g-5">
                            <span class="fs-14 fw-500 text-para-text">{{__('Total Valid Requests:')}}</span>
                            <span class="fs-14 fw-600 text-success">{{ $validCount }}</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center g-10">
                        <a href="{{ route('admin.free_consultations.index') }}" class="flipBtn sf-flipBtn-secondary flex-shrink-0">
                            <span>{{__('Cancel')}}</span>
                        </a>
                        <button type="submit" class="flipBtn sf-flipBtn-primary flex-shrink-0">
                            <span>{{__('Save Requests')}} ({{ $validCount }})</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('script')
    <script>
        $('#save-leads-form').on('submit', function(e) {
            e.preventDefault();
            let form = $(this);
            let formData = new FormData(form[0]);
            
            $.ajax({
                url: form.attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === true) {
                        toastr.success(response.message || '{{__("Consultation requests imported successfully")}}');
                        setTimeout(function() {
                            window.location.href = '{{ route("admin.free_consultations.index") }}';
                        }, 1500);
                    } else {
                        toastr.error(response.message || '{{__("Something went wrong")}}');
                    }
                },
                error: function(xhr) {
                    let message = '{{__("Something went wrong")}}';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    toastr.error(message);
                }
            });
        });
    </script>
@endpush
