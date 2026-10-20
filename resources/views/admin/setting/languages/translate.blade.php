@extends('layouts.app')
@push('title')
    {{$pageTitle}}
@endpush
@section('content')
    <div class="p-30">
        <div class="">
            <h4 class="fs-24 fw-500 lh-34 text-black pb-16">{{ __($pageTitle) }}</h4>
            <div class="row bd-c-ebedf0 bd-half bd-ra-25 bg-white h-100 p-sm-30 p-15">
                <input type="hidden" id="language-route" value="{{ route('admin.setting.languages.index') }}">
                <div class="col-lg-12">
                    <div class="d-flex flex-wrap gap-2 item-title justify-content-end mb-20">
                        <a class="flipBtn sf-flipBtn-primary"
                           href="{{route('admin.setting.languages.download', $language->id)}}"
                           title="{{ __('Download File') }}">
                            {{ __('Download File') }}
                        </a>
                        <button type="button" class="flipBtn sf-flipBtn-primary"
                                data-bs-toggle="modal"
                                data-bs-target="#importFile"
                                title="{{ __('Import File') }}">
                            {{ __('Import File') }}
                        </button>
                        <button type="button"
                                class="flipBtn sf-flipBtn-primary"
                                data-bs-toggle="modal" data-bs-target="#importModal" title="Import Keywords">
                            {{__('Import Keywords')}}
                        </button>
                        <button type="button"
                                class="flipBtn sf-flipBtn-primary addmore">
                            <i class="fa fa-plus"></i>
                            {{__('Add More')}}
                        </button>
                    </div>
                    <div class="justify-content-center mb-20 row">
                        <div class="col-md-6">
                            <form id="search-form">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="{{__('Search Key or Value')}}">
                                    <button class="flipBtn sf-flipBtn-secondary" type="submit">{{__('Search')}}</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="row">
                        <div id="translations-container">
                            @include('admin.setting.languages.partials.translations_table')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Modal section start -->
    <div class="modal fade" id="importModal" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 bd-ra-4 p-sm-25 p-15">
                <form class="ajax" action="{{ route('admin.setting.languages.import') }}" method="POST"
                      data-handler="languageHandler">
                    @csrf
                    <input type="hidden" name="current" value="{{ $language->iso_code }}">
                    <div
                        class="bd-b-one bd-c-stroke pb-20 mb-20 d-flex align-items-center flex-wrap justify-content-between g-10">
                        <h2 class="fs-18 fw-600 lh-22 text-title-text">{{ __('Import Language') }}</h2>
                        <div class="mClose">
                            <button type="button"
                                    class="bd-one bd-c-stroke rounded-circle w-25 h-25 bg-transparent text-para-text"
                                    data-bs-dismiss="modal" aria-label="Close">
                                <i class="fa-solid fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <div class="pb-20">
                        <div class="mb-30">
                                <span class="text-danger text-center">{{ __('Note: If you import keywords, your current
                                    keywords will be deleted and replaced by the imported keywords.') }}</span>
                        </div>
                        <div class="">
                            <label for="status" class="label-text-title color-heading font-medium mb-2">
                                {{ __('Language') }} </label>
                            <select name="import" class="sf-select flex-shrink-0 export" id="inputGroupSelect02">
                                <option value=""> {{ __('Select Option') }} </option>
                                @foreach ($languages as $lang)
                                    <option value="{{ $lang->iso_code }}">{{ __($lang->language) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="pt-20 d-flex g-10 bd-t-one bd-c-stroke">
                        <button type="button" class="sf-btn-secondary" data-bs-dismiss="modal" title="Back">{{
                        __('Back')
                        }}</button>
                        <button type="submit" class="flipBtn sf-flipBtn-primary" title="Submit">{{ __('Import') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="importFile" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 bd-ra-4 p-10">
                <form class="ajax" action="{{ route('admin.setting.languages.upload', $language->id) }}" method="POST"
                      enctype="multipart/form-data" data-handler="languageHandler">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Upload Translated File') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <span class="iconify" data-icon="akar-icons:cross"></span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-30 text-center">
                        <span class="text-warning">
                            {{ __('Upload a valid JSON translation file. Existing translations will be merged with the uploaded file. Keys in the uploaded file will overwrite existing keys.') }}
                        </span>
                        </div>
                        <div class="col-md-12 mb-25">
                            <label for="file" class="label-text-title color-heading font-medium mb-2">
                                {{ __('Select JSON File') }}
                            </label>
                            <input type="file" name="file" class="form-control" accept=".json" required>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-start">
                        <button type="button"
                                class="sf-flipBtn sf-flipBtn-secondary"
                                data-bs-dismiss="modal" title="Back">{{ __('Back') }}</button>
                        <button type="submit"
                                class="sf-flipBtn sf-flipBtn-primary"
                                title="Submit">{{ __('Upload') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <input type="hidden" id="updateLangItemRoute"
           value="{{ route('admin.setting.languages.update.translate', [$language->id]) }}">
    <input type="hidden" id="language-translate-route"
           value="{{ route('admin.setting.languages.translate', [$language->id]) }}">
@endsection

@push('script')
    <script src="{{asset('admin/custom/js/languages.js')}}"></script>
@endpush
