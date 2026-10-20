<div class="bd-b-one bd-c-stroke pb-20 mb-20 d-flex align-items-center flex-wrap justify-content-between g-10">
    <h2 class="fs-18 fw-600 lh-22 text-title-black">{{ __('Update Course Module') }}</h2>
    <div class="mClose">
        <button type="button"
                class="bd-one bd-c-stroke rounded-circle w-24 h-24 bg-transparent text-para-text fs-13"
                data-bs-dismiss="modal" aria-label="Close">
            <i class="fa-solid fa-times"></i>
        </button>
    </div>
</div>
<form class="ajax reset" action="{{ route('admin.courses.lessons.store', $courseLesson->course_id) }}"
      method="post"
      data-handler="commonResponseRedirect" data-redirect-url="{{route('admin.courses.lessons.index', encodeId($courseLesson->course_id))}}">
    @csrf
    <input type="hidden" name="id" value="{{$courseLesson->id}}">
    <div class="row rg-20">
        <div class="">
            <label for="title" class="zForm-label-alt">{{ __('Title') }} <span
                    class="text-danger">*</span></label>
            <input type="text" value="{{$courseLesson->title}}" name="title" id="title" placeholder="{{ __('Type Title') }}"
                   class="form-control zForm-control-alt">
        </div>
        <div class="">
            <label for="description" class="zForm-label-alt">{{ __('Description') }} <span
                    class="text-danger">*</span></label>
            <textarea name="description" id="description" placeholder="{{ __('Type Description') }}"
                      class="form-control zForm-control-alt">{{ $courseLesson->description }}</textarea>
        </div>
        <div class="col-md-4"><label class="zForm-label-alt">Content type</label><select name="lesson_type" class="form-control zForm-control-alt">@foreach(['video','text','reading','download','live'] as $type)<option value="{{ $type }}" @selected(($courseLesson->lesson_type ?? 'video') === $type)>{{ ucfirst($type) }}</option>@endforeach</select></div>
        <div class="col-md-4"><label class="zForm-label-alt">Order</label><input type="number" min="0" name="sort_order" value="{{ $courseLesson->sort_order ?? 0 }}" class="form-control zForm-control-alt"></div>
        <div class="col-md-4"><label class="zForm-label-alt">Visibility</label><select name="publication_state" class="form-control zForm-control-alt"><option value="draft" @selected(($courseLesson->publication_state ?? 'draft') === 'draft')>Draft</option><option value="published" @selected(($courseLesson->publication_state ?? '') === 'published')>Published</option></select></div>
    </div>
    <div class="bd-c-stroke-2 justify-content-between align-items-center text-end pt-15">
        <button type="submit" class="sf-btn-primary flex-shrink-0">{{__('Update')}}</button>
    </div>
</form>
