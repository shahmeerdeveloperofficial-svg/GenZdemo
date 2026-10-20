<div class="row rg-20">
    <div class="col-md-6">
        <label for="first_name" class="zForm-label-alt">{{__('First Name')}} <span class="text-danger">*</span></label>
        <input type="text" name="first_name" id="first_name" class="form-control zForm-control-alt" placeholder="{{__('Enter First Name')}}" required>
    </div>
    <div class="col-md-6">
        <label for="last_name" class="zForm-label-alt">{{__('Last Name')}} <span class="text-danger">*</span></label>
        <input type="text" name="last_name" id="last_name" class="form-control zForm-control-alt" placeholder="{{__('Enter Last Name')}}" required>
    </div>
    <div class="col-md-6">
        <label for="email" class="zForm-label-alt">{{__('Email Address')}} <span class="text-danger">*</span></label>
        <input type="email" name="email" id="email" class="form-control zForm-control-alt" placeholder="{{__('Enter Email Address')}}" required>
    </div>
    <div class="col-md-6">
        <label for="mobile" class="zForm-label-alt">{{__('Mobile Number')}} <span class="text-danger">*</span></label>
        <input type="text" name="mobile" id="mobile" class="form-control zForm-control-alt" placeholder="{{__('Enter Mobile Number')}}" required>
    </div>
    <div class="col-md-6">
        <label for="study_level_id" class="zForm-label-alt">{{__('Study Level')}} <span class="text-danger">*</span></label>
        <select name="study_level_id" id="study_level_id" class="sf-select-checkbox" required>
            <option value="">{{__('Select Study Level')}}</option>
            @foreach(\App\Models\StudyLevel::where('status', STATUS_ACTIVE)->get() as $level)
                <option value="{{ $level->id }}">{{ $level->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6">
        <label for="consultation_type" class="zForm-label-alt">{{__('Consultation Type')}}</label>
        <select name="consultation_type" id="consultation_type" class="sf-select-checkbox">
            <option value="">{{__('Select Consultation Type')}}</option>
            <option value="{{ CONSULTATION_TYPE_PHYSICAL }}">{{__('Physical')}}</option>
            <option value="{{ CONSULTATION_TYPE_VIRTUAL }}">{{__('Virtual')}}</option>
        </select>
    </div>
    <div class="col-md-6">
        <label for="fund_type" class="zForm-label-alt">{{__('Education Fund Type')}}</label>
        <select name="fund_type" id="fund_type" class="sf-select-checkbox">
            <option value="">{{__('Select Fund Type')}}</option>
            <option value="{{ FUND_TYPE_SELF }}">{{__('Self')}}</option>
            <option value="{{ FUND_TYPE_PARENTS }}">{{__('Parents')}}</option>
            <option value="{{ FUND_TYPE_SEEKING_SCHOLARSHIP }}">{{__('Seeking Scholarship')}}</option>
            <option value="{{ FUND_TYPE_BANK_LOAN }}">{{__('Bank Loan')}}</option>
            <option value="{{ FUND_TYPE_EMPLOYER_SCHOLARSHIP }}">{{__('Employer Scholarship')}}</option>
            <option value="{{ FUND_TYPE_OTHER }}">{{__('Other')}}</option>
        </select>
    </div>
    <div class="col-md-6">
        <label for="country_ids" class="zForm-label-alt">{{__('Preferred Study Destination')}}</label>
        <select name="country_ids[]" id="country_ids" class="sf-select-checkbox" multiple>
            @foreach(\App\Models\Country::where('status', STATUS_ACTIVE)->get() as $country)
                <option value="{{ $country->id }}">{{ $country->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-12">
        <label for="narration" class="zForm-label-alt">{{__('Narration')}}</label>
        <textarea name="narration" id="narration" class="form-control zForm-control-alt" rows="3" placeholder="{{__('Enter Narration')}}"></textarea>
    </div>
</div>
