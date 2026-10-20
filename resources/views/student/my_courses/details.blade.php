@extends('layouts.app')
@push('title')
    {{ $pageTitle }}
@endpush
@push('style')
    <!-- Plyr CSS -->
    <link rel="stylesheet" href="{{asset('assets/css/plyr.css')}}"/>
@endpush
@section('content')
    <div class="p-sm-30 p-15">
        <!-- Course Content -->
                <h4 class="fs-24 fw-700 lh-32 text-title-text pb-20">{{ $course?->title }}</h4>
        <div class="row rg-20">
            @if(\Illuminate\Support\Facades\Schema::hasTable('lms_quizzes'))<div class="col-12"><a class="btn btn-outline-primary" href="{{ route('student.my_courses.quizzes.index',encodeId($course->enrollment_id)) }}">{{ __('Course quizzes') }}</a></div>@endif
            @if(\Illuminate\Support\Facades\Schema::hasTable('lms_assignments'))<div class="col-12"><a class="btn btn-outline-primary" href="{{ route('student.my_courses.assignments.index',encodeId($course->enrollment_id)) }}">{{ __('Course assignments') }}</a></div>@endif
            <div class="col-12"><div class="progress" role="progressbar" aria-label="{{ __('Course progress') }}" aria-valuenow="{{ $progressPercent }}" aria-valuemin="0" aria-valuemax="100"><div class="progress-bar" style="width: {{ $progressPercent }}%">{{ $progressPercent }}%</div></div></div>
            @if($liveSessions->isNotEmpty())
                <div class="col-12"><section class="p-3 bg-white rounded border"><h5>{{ __('Live classes') }}</h5>@foreach($liveSessions as $session)<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 border-top py-3"><div><strong>{{ $session->title }}</strong><div class="small text-muted">{{ $session->starts_at->timezone($session->timezone)->format('D, M j, Y · g:i A T') }} · {{ $session->duration_minutes }} {{ __('minutes') }} · {{ $session->provider === 'external' ? __('External meeting') : ucfirst(str_replace('_',' ',$session->provider)) }}</div>@if($session->attendance->first())<span class="badge bg-info">{{ ucfirst($session->attendance->first()->attendance_status) }}</span>@endif</div><div class="d-flex gap-2">@if($session->starts_at->copy()->timezone($session->timezone)->addMinutes($session->duration_minutes)->isPast())<a class="btn btn-outline-primary" href="{{ route('student.my_courses.live_sessions.recording',['enrollment_id'=>encodeId($course->enrollment_id),'session_id'=>$session->id]) }}">{{ __('View recording') }}</a>@else<form class="live-session-join" method="POST" action="{{ route('student.my_courses.live_sessions.join',['enrollment_id'=>encodeId($course->enrollment_id),'session_id'=>$session->id]) }}" data-heartbeat="{{ route('student.my_courses.live_sessions.heartbeat',['enrollment_id'=>encodeId($course->enrollment_id),'session_id'=>$session->id]) }}">@csrf<button class="btn btn-primary" type="submit">{{ __('Join class') }}</button></form>@endif</div></div>@endforeach</section></div>
            @endif
            <div class="col-xll-3 col-lg-5 col-md-6">
                <div class="accordion zAccordion-reset zAccordion-one" id="accordionModule">
                    @foreach($course->lessons as $lesson)
                        <div class="item-wrap lesson-item">
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button
                                        class="accordion-button {{ $lesson->id == $currentLessonId ? '' : 'collapsed' }}"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#collapse-lesson-{{$lesson->id}}"
                                        aria-expanded="{{ $lesson->id == $currentLessonId ? 'true' : 'false' }}"
                                        aria-controls="collapse-lesson-{{$lesson->id}}">
                                        {{ $lesson->title }}
                                    </button>
                                </h2>
                                <div id="collapse-lesson-{{$lesson->id}}"
                                     class="accordion-collapse collapse {{ $lesson->id == $currentLessonId ? 'show' : '' }}"
                                     data-bs-parent="#accordionModule">
                                    <div class="accordion-body">
                                        <p class="fs-12 fw-500 lh-18 text-para-text pb-20">
                                        {!! nl2br(e($lesson->description)) !!}
                                        </p>
                                        <ul class="course-modules-list lecture-list">
                                            @foreach($lesson->lectures as $lecture)
                                                <li class="item">
                                                    <div class="content-wrap">
                                                        <p class="fs-12 fw-500 lh-24 text-para-text pt-11 lecture-number">{{addLeadingZero($loop->iteration)}}</p>
                                                        <div class="moduleDetails-content-alt">
                                                            <div class="d-flex align-items-center g-10 titleWrap">
                                                                <p class="px-20 w-100">{{$lecture->title}}</p>
                                                                <button class="{{ $lecture->id == $currentLectureId ? '' : 'collapsed' }}" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-lecture-{{$lecture->id}}" aria-expanded="{{ $lecture->id == $currentLectureId ? 'true' : 'false' }}" aria-controls="collapse-lecture-{{$lecture->id}}">
                                                                    <img src="{{asset('assets/images/icon/accordion-icon-1-alt.svg')}}" alt="" class="defaultIcon"/>
                                                                </button>
                                                            </div>

                                                            <div
                                        class="collapse {{ $lecture->id == $currentLectureId ? 'show' : '' }}"
                                                                id="collapse-lecture-{{$lecture->id}}">
                                                                @if(count($lecture->resources))
                                                                    <div class="card card-body">
                                                                        <ul class="zList-pb-5">
                                                                            @foreach($lecture->resources as $resource)
                                                                                <li class="d-flex justify-content-between align-items-center g-10">
                                                                                    <div class="d-flex g-7">
                                                                                        <div class="d-flex">
                                                                                            <!-- Determine icon based on resource status -->
                                                                                            @if(in_array($resource->id, $finishedResourceIds))
                                                                                                <img
                                                                                                    src="{{ asset('assets/images/icon/video-complete.svg') }}"
                                                                                                    alt="{{__('Finished')}}"/>
                                                                                            @elseif($resource->id == $currentResourceId)
                                                                                                <img
                                                                                                    src="{{ asset('assets/images/icon/video-processing.svg') }}"
                                                                                                    alt="{{__('Current')}}"/>
                                                                                            @else
                                                                                                <img
                                                                                                    src="{{ asset('assets/images/icon/video-incomplete.svg') }}"
                                                                                                    alt="{{__('Unfinished')}}"/>
                                                                                            @endif
                                                                                        </div>
                                                                                        <a href="{{ route('student.my_courses.view', ['enrollment_id' => encodeId($course->enrollment_id), 'lecture_id' => encodeId($lecture->id), 'resource_id' => encodeId($resource->id)]) }}"
                                                                                           title="{{ __('Play') }}">
                                                                                            <p class="fs-15 fw-500 lh-24 text-para-text">{{ $resource->title }}</p>
                                                                                        </a>
                                                                                    </div>
                                                                                    <div
                                                                                        class="d-flex align-items-center g-5">
                                                                                        <p class="fs-15 fw-500 lh-24 text-para-text">
                                                                                            @if($resource->duration)
                                                                                                {{ getResourceDuration($resource->duration) }}
                                                                                            @else
                                                                                                --:--
                                                                                            @endif
                                                                                        </p>
                                                                                    </div>
                                                                                </li>
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                @else
                                                                    <div class="card card-body text-center">
                                                                        {{__('No resource')}}
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if(!count($course->lessons))
                    <div
                        class="align-items-center bd-ra-5 bg-white course-details-info d-flex flex-column g-10 h-100 justify-content-center p-16">
                        <span class="text-center">{{__('No module found')}}</span>
                    </div>
                @endif
            </div>

            <div class="col-xll-9 col-lg-7 col-md-6">
                @if($currentResource)
                    <div>
                        @if($currentResource->resource_type == RESOURCE_TYPE_LOCAL)
                            <!-- Local Video Player -->
                            <video class="w-100" id="video-player" controls data-progress-url="{{ route('student.my_courses.save_progress',['enrollment_id'=>encodeId($course->enrollment_id),'resource_id'=>encodeId($currentResource->id)]) }}" data-resume-at="{{ $currentProgress?->last_position_seconds ?? 0 }}">
                                <source src="{{ route('student.my_courses.resource', ['enrollment_id' => encodeId($course->enrollment_id), 'resource_id' => encodeId($currentResource->id)]) }}" type="{{ \App\Models\FileManager::find($currentResource->resource)?->file_type ?? 'video/mp4' }}">
                                Your browser does not support the video tag.
                            </video>
                        @elseif($currentResource->resource_type == RESOURCE_TYPE_PDF)
                            <!-- PDF Viewer -->
                            <div class="p-md-30 p-sm-25 p-15 bd-one bd-c-stroke bd-ra-5 bg-white">
                                <iframe src="{{ route('student.my_courses.resource', ['enrollment_id' => encodeId($course->enrollment_id), 'resource_id' => encodeId($currentResource->id)]) }}" class="w-100" height="600"
                                        style="border: none;"></iframe>
                            </div>
                        @elseif($currentResource->resource_type == RESOURCE_TYPE_IMAGE)
                            <!-- Image Viewer -->
                            <img src="{{ route('student.my_courses.resource', ['enrollment_id' => encodeId($course->enrollment_id), 'resource_id' => encodeId($currentResource->id)]) }}"
                                 alt="{{ $currentResource->title }}" class="w-100">
                        @elseif($currentResource->resource_type == RESOURCE_TYPE_AUDIO)
                            <!-- Audio Player -->
                            <div class="p-md-30 p-sm-25 p-15 bd-one bd-c-stroke bd-ra-5 bg-white">
                                <audio class="w-100" id="audio-player" controls>
                                    <source src="{{ route('student.my_courses.resource', ['enrollment_id' => encodeId($course->enrollment_id), 'resource_id' => encodeId($currentResource->id)]) }}" type="{{ \App\Models\FileManager::find($currentResource->resource)?->file_type ?? 'audio/mpeg' }}">
                                    Your browser does not support the audio tag.
                                </audio>
                            </div>
                        @elseif($currentResource->resource_type == RESOURCE_TYPE_YOUTUBE_ID)
                            <!-- YouTube Player -->
                            <div class="video-player-area">
                                <div class="youtube-player">
                                    <div id="youtube-player-video" class="youtube-video" data-video-id="{{ $currentResource->resource }}"></div>

                                    <button class="youtube-video-overBtn"><i class="fa fa-play"></i></button>

                                    <div class="youtube-player-controls">
                                        <button class="play-button">
                                            <i class="fa fa-play"></i>
                                        </button>
                                        <progress class="progress-bar" min="0" max="100" value="0"></progress>
                                        <span class="progress-text"></span>

                                        <button class="sound-button">
                                            <i class="fa fa-volume-up"></i>
                                        </button>
                                        <progress class="sound-bar" min="0" max="100" value="0"></progress>
                                        <button class="fullscreen-button">
                                            <i class="fa fa-expand"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @elseif($currentResource->resource_type == RESOURCE_TYPE_SLIDE)
                            <!-- Slide Content (e.g., HTML or embedded iframe) -->
                            <div class="p-md-30 p-sm-25 p-15 bd-one bd-c-stroke bd-ra-5 bg-white">
                            <iframe src="{{ $currentResource->resource }}" class="w-100" height="600" title="{{ $currentResource->title }}" loading="lazy" referrerpolicy="no-referrer"></iframe>
                            </div>
                        @endif
                    </div>
                @endif
                @if($currentResource)
                    <form method="POST" action="{{ route('student.my_courses.complete_resource', ['enrollment_id' => encodeId($course->enrollment_id), 'resource_id' => encodeId($currentResource->id)]) }}" class="mt-3">
                        @csrf
                        <button class="flipBtn sf-flipBtn-primary" type="submit">{{ in_array($currentResource->id, $finishedResourceIds) ? __('Completed') : __('Mark as complete') }}</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{asset('assets/js/plyr.js')}}"></script>
    <script src="{{asset('common/js/youtube-player.js')}}"></script>
    <script src="{{asset('common/js/player.js')}}"></script>
    <script>
        (() => {
            const player = document.getElementById('video-player');
            if (!player || !player.dataset.progressUrl) return;
            const token = document.querySelector('meta[name="csrf-token"]')?.content;
            let lastSentAt = Date.now();
            let timer;
            const save = (completed = false) => {
                const now = Date.now();
                const elapsed = Math.min(120, Math.max(0, Math.floor((now - lastSentAt) / 1000)));
                lastSentAt = now;
                fetch(player.dataset.progressUrl, {method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':token,'Accept':'application/json'},body:JSON.stringify({last_position_seconds:Math.floor(player.currentTime || 0),time_spent_seconds:elapsed,completed})}).catch(() => {});
            };
            player.addEventListener('loadedmetadata', () => { const at = Number(player.dataset.resumeAt || 0); if (at > 0 && at < player.duration) player.currentTime = at; });
            player.addEventListener('timeupdate', () => { if (!timer) timer = setTimeout(() => { save(); timer = null; }, 15000); });
            player.addEventListener('pause', () => save());
            player.addEventListener('ended', () => save(true));
            window.addEventListener('pagehide', () => save());
        })();
        document.querySelectorAll('.live-session-join').forEach(form => form.addEventListener('submit', async event => {
            event.preventDefault();
            const meetingWindow = window.open('about:blank', '_blank');
            try {
                const response = await fetch(form.action, {method:'POST',credentials:'same-origin',headers:{'X-CSRF-TOKEN':form.querySelector('[name="_token"]').value,'Accept':'application/json'}});
                const result = await response.json();
                if (!response.ok || !result.meeting_url) throw new Error('Unable to join this session.');
                meetingWindow.location = result.meeting_url;
                const heartbeat = async () => { try { await fetch(form.dataset.heartbeat,{method:'POST',credentials:'same-origin',headers:{'X-CSRF-TOKEN':form.querySelector('[name="_token"]').value,'Accept':'application/json'}}); } catch (_) {} };
                await heartbeat();
                const timer = setInterval(() => { if (meetingWindow.closed) { clearInterval(timer); return; } heartbeat(); }, 60000);
            } catch (error) { if (meetingWindow) meetingWindow.close(); window.GenzToast.error(error.message); }
        }));
    </script>
@endpush
