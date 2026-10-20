<?php

namespace App\Http\Controllers\Admin\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLecture;
use App\Models\CourseLesson;
use App\Models\CourseResource;
use App\Models\FileManager;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseResourceController extends Controller
{
    use ResponseTrait;

    private function parents($courseId, $lessonId, $lectureId): array
    {
        $courseQuery = Course::whereKey((int) $courseId);
        if ((int) auth()->user()->role === USER_ROLE_INSTRUCTOR) $courseQuery->where('created_by', auth()->id());
        $course = $courseQuery->firstOrFail();
        $lesson = CourseLesson::where('course_id', $course->id)->whereKey($lessonId)->firstOrFail();
        $lecture = CourseLecture::where('course_id', $course->id)->where('course_lesson_id', $lesson->id)->whereKey($lectureId)->firstOrFail();
        return [$course, $lesson, $lecture];
    }

    public function store(Request $request, $course_id, $lesson_id, $lecture_id)
    {
        [$course, $lesson, $lecture] = $this->parents($course_id, $lesson_id, $lecture_id);
        $type = (int) $request->input('resource_type');
        $fileMimes = match ($type) {
            RESOURCE_TYPE_LOCAL => 'mp4,mov,mkv,avi,webm', RESOURCE_TYPE_PDF => 'pdf',
            RESOURCE_TYPE_IMAGE => 'jpg,jpeg,png,gif,webp', RESOURCE_TYPE_AUDIO => 'mp3,wav,ogg,m4a', default => null,
        };
        $data = $request->validate([
            'resource_name' => ['required','string','min:2','max:195'],
            'resource_type' => ['required','integer','in:'.implode(',', [RESOURCE_TYPE_LOCAL,RESOURCE_TYPE_YOUTUBE_ID,RESOURCE_TYPE_PDF,RESOURCE_TYPE_IMAGE,RESOURCE_TYPE_AUDIO,RESOURCE_TYPE_SLIDE])],
            'duration' => ['nullable','integer','min:0','max:100000'],
            'file' => [$fileMimes ? 'required' : 'nullable','file','max:512000', ...($fileMimes ? ['mimes:'.$fileMimes] : [])],
            'youtube_id' => ['required_if:resource_type,'.RESOURCE_TYPE_YOUTUBE_ID,'nullable','string','max:255'],
            'google_slide_link' => ['required_if:resource_type,'.RESOURCE_TYPE_SLIDE,'nullable','url','max:2000'],
            'sort_order' => ['nullable','integer','min:0','max:100000'],
            'publication_state' => ['nullable','in:draft,published'], 'is_preview' => ['nullable','boolean'],
            'completion_required' => ['nullable','boolean'],
        ]);

        $storedPath = null;
        DB::beginTransaction();
        try {
            $resource = new CourseResource();
            $resource->course_id = $course->id; $resource->course_lesson_id = $lesson->id; $resource->course_lecture_id = $lecture->id;
            $resource->title = $data['resource_name']; $resource->resource_type = $type; $resource->duration = $data['duration'] ?? 0;
            if (in_array($type, [RESOURCE_TYPE_LOCAL, RESOURCE_TYPE_PDF, RESOURCE_TYPE_IMAGE, RESOURCE_TYPE_AUDIO], true)) {
                $file = $request->file('file');
                $filename = Str::uuid().'.'.$file->getClientOriginalExtension();
                $storedPath = $file->storeAs('lms/courses/'.$course->id, $filename, 'private');
                $fileRecord = FileManager::create([
                    'file_type' => $file->getMimeType(), 'storage_type' => 'private', 'original_name' => $file->getClientOriginalName(),
                    'file_name' => $filename, 'user_id' => auth()->id(), 'path' => $storedPath,
                    'extension' => strtolower($file->getClientOriginalExtension()), 'size' => $file->getSize(),
                ]);
                $resource->resource = (string) $fileRecord->id;
            } elseif ($type === RESOURCE_TYPE_YOUTUBE_ID) {
                $resource->resource = trim($data['youtube_id']);
            } else {
                $resource->resource = $data['google_slide_link'];
            }
            if (Schema::hasColumn('course_resources','sort_order')) $resource->sort_order = $data['sort_order'] ?? 0;
            if (Schema::hasColumn('course_resources','publication_state')) $resource->publication_state = (int) auth()->user()->role === USER_ROLE_INSTRUCTOR ? 'draft' : ($data['publication_state'] ?? 'draft');
            if (Schema::hasColumn('course_resources','is_preview')) $resource->is_preview = (bool) ($data['is_preview'] ?? false);
            if (Schema::hasColumn('course_resources','completion_required')) $resource->completion_required = (bool) ($data['completion_required'] ?? true);
            $resource->save();
            DB::commit();
            return $this->success([], __('Resource created successfully!'));
        } catch (\Throwable $exception) {
            DB::rollBack();
            if ($storedPath) Storage::disk('private')->delete($storedPath);
            report($exception);
            return $this->error([], __('Something went wrong! Please try again.'));
        }
    }

    public function view($course_id, $lesson_id, $lecture_id, $id)
    {
        [$course, $lesson, $lecture] = $this->parents($course_id, $lesson_id, $lecture_id);
        $resource = CourseResource::where('course_id',$course->id)->where('course_lesson_id',$lesson->id)->where('course_lecture_id',$lecture->id)->whereKey($id)->firstOrFail();
        return view('admin.courses.lessons.resource-view', compact('resource'));
    }

    public function delete($course_id, $lesson_id, $lecture_id, $id)
    {
        [$course, $lesson, $lecture] = $this->parents($course_id, $lesson_id, $lecture_id);
        CourseResource::where('course_id',$course->id)->where('course_lesson_id',$lesson->id)->where('course_lecture_id',$lecture->id)->whereKey($id)->firstOrFail()->delete();
        return $this->success([], __(DELETED_SUCCESSFULLY));
    }
}
