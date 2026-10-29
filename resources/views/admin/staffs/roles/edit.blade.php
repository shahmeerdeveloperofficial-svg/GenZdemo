<div class="bd-b-one bd-c-stroke pb-20 mb-20 d-flex align-items-center justify-content-between">
    <h2 class="fs-18 fw-600 lh-22 text-title-text">{{ __('Edit custom role') }}</h2>
    <button type="button" class="bd-one bd-c-stroke rounded-circle w-25 h-25 bg-transparent text-para-text" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-times"></i></button>
</div>
<form class="ajax reset" action="{{ route('admin.staffs.roles.update', $role->id) }}" method="post" data-handler="settingCommonHandler">
    @method('PATCH')
    @csrf
    <div class="mb-3">
        <label class="zForm-label-alt">{{ __('Name') }} <span class="text-danger">*</span></label>
        <input type="text" name="name" value="{{ $role->name }}" class="form-control zForm-control-alt" required maxlength="120">
    </div>
    <div class="mb-4">
        <label class="zForm-label-alt">{{ __('Status') }} <span class="text-danger">*</span></label>
        <select class="sf-select-without-search" name="status">
            <option value="{{ STATUS_ACTIVE }}" @selected($role->status == STATUS_ACTIVE)>{{ __('Active') }}</option>
            <option value="{{ STATUS_DEACTIVATE }}" @selected($role->status == STATUS_DEACTIVATE)>{{ __('Deactivate') }}</option>
        </select>
    </div>
    <h5 class="fs-16 fw-700 lh-24 text-title-text">{{ __('Module permissions') }}</h5>
    <p class="small text-muted">{{ __('Select what this role can view, add, edit, or delete. Unchecked actions are blocked.') }}</p>
    @include('admin.staffs.roles._permission_matrix')
    <div class="text-end pt-3"><button type="submit" class="flipBtn sf-flipBtn-primary">{{ __('Save role') }}</button></div>
</form>
