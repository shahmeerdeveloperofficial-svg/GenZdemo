<div class="bd-b-one bd-c-stroke pb-20 mb-20 d-flex align-items-center flex-wrap justify-content-between g-10">
    <h2 class="fs-18 fw-600 lh-22 text-title-text">{{ __('Edit Student') }}</h2>
    <button type="button" class="bd-one bd-c-stroke rounded-circle w-25 h-25 bg-transparent text-para-text"
            data-bs-dismiss="modal" aria-label="{{ __('Close') }}">
        <i class="fa-solid fa-times"></i>
    </button>
</div>

<form class="ajax reset" action="{{ route('admin.students.update', encodeId($user->id)) }}" method="post"
      data-handler="commonResponseForModal">
    @csrf
    @method('PATCH')
    <div class="row rg-20 mb-20">
        <div class="col-md-6">
            <label for="edit_student_first_name" class="zForm-label-alt">{{ __('First Name') }} <span class="text-danger">*</span></label>
            <input type="text" name="first_name" id="edit_student_first_name" value="{{ $user->first_name }}"
                   class="form-control zForm-control-alt" required maxlength="120">
        </div>
        <div class="col-md-6">
            <label for="edit_student_last_name" class="zForm-label-alt">{{ __('Last Name') }} <span class="text-danger">*</span></label>
            <input type="text" name="last_name" id="edit_student_last_name" value="{{ $user->last_name }}"
                   class="form-control zForm-control-alt" required maxlength="120">
        </div>
        <div class="col-md-6">
            <label for="edit_student_email" class="zForm-label-alt">{{ __('Email') }} <span class="text-danger">*</span></label>
            <input type="email" name="email" id="edit_student_email" value="{{ $user->email }}"
                   class="form-control zForm-control-alt" required maxlength="255">
        </div>
        <div class="col-md-6">
            <label for="edit_student_mobile" class="zForm-label-alt">{{ __('Mobile') }} <span class="text-danger">*</span></label>
            <input type="text" name="mobile" id="edit_student_mobile" value="{{ $user->mobile }}"
                   class="form-control zForm-control-alt" required minlength="6" maxlength="30">
        </div>
        <div class="col-md-6">
            <label for="edit_student_password" class="zForm-label-alt">{{ __('New Password') }}</label>
            <input type="password" name="password" id="edit_student_password" autocomplete="new-password"
                   placeholder="{{ __('Leave blank to keep the current password') }}"
                   class="form-control zForm-control-alt" minlength="8" maxlength="255">
            <small class="fs-12 fst-italic">{{ __('Leave blank to keep the current password.') }}</small>
        </div>
        <div class="col-md-6">
            <label for="edit_student_status" class="zForm-label-alt">{{ __('Status') }} <span class="text-danger">*</span></label>
            <select class="sf-select-without-search" id="edit_student_status" name="status" required>
                <option value="{{ STATUS_ACTIVE }}" {{ (int) $user->status === STATUS_ACTIVE ? 'selected' : '' }}>{{ __('Active') }}</option>
                <option value="{{ STATUS_DEACTIVATE }}" {{ (int) $user->status === STATUS_DEACTIVATE ? 'selected' : '' }}>{{ __('Deactivate') }}</option>
                <option value="{{ STATUS_SUSPENDED }}" {{ (int) $user->status === STATUS_SUSPENDED ? 'selected' : '' }}>{{ __('Suspended') }}</option>
            </select>
        </div>
    </div>
    <div class="text-end pt-15">
        <button type="submit" class="flipBtn sf-flipBtn-primary flex-shrink-0">
            <span class="d-none h-25 spinner-border w-25" role="status"><span class="visually-hidden"></span></span>
            {{ __('Save Changes') }}
        </button>
    </div>
</form>
