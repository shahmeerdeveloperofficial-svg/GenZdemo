<?php

namespace App\Http\Controllers\Admin\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLesson;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CourseLessonController extends Controller
{
    use ResponseTrait;

    private function course($id): Course
    {
        $query = Course::whereKey((int) $id);
        if ((int) auth()->user()->role === USER_ROLE_INSTRUCTOR) $query->where('created_by', auth()->id());
        return $query->firstOrFail();
    }

    public function index($course_id)
    {
        $data['course'] = $this->course(decodeId($course_id))->load(['lessons.lectures.resources', 'enrolls.user']);
        $data['courseBenefits'] = collect($data['course']->course_benefits ?? [])
            ->map(fn ($benefit) => is_string($benefit)
                ? ['name' => __('Benefit'), 'value' => $benefit]
                : $benefit)
            ->filter(fn ($benefit) => is_array($benefit)
                && isset($benefit['name'])
                && array_key_exists('value', $benefit))
            ->values();
        $data['batches'] = Schema::hasTable('course_batches') ? $data['course']->batches()->get() : collect();
        $data['liveSessions'] = Schema::hasTable('course_live_sessions') ? \App\Models\CourseLiveSession::where('course_id',$data['course']->id)->with(['batch','attendance.enrollment.user'])->orderByDesc('starts_at')->limit(50)->get() : collect();
        $data['pageTitle'] = __('Course Curriculum');
        $data['showManageCourse'] = 'show';
        $data['activeCourse'] = 'active';
        return view('admin.courses.lessons.index', $data);
    }

    public function edit($course_id, $id)
    {
        $course = $this->course($course_id);
        $lesson = CourseLesson::where('course_id', $course->id)->whereKey($id)->firstOrFail();
        return view('admin.courses.lessons.edit', ['courseLesson' => $lesson]);
    }

    public function store(Request $request, $course_id)
    {
        $course = $this->course($course_id);
        $data = $request->validate([
            'id' => ['nullable','integer'],
            'title' => ['required','string','max:195'], 'description' => ['required','string','min:10'],
            'sort_order' => ['nullable','integer','min:0','max:100000'],
            'lesson_type' => ['nullable','in:video,text,reading,download,live'],
            'publication_state' => ['nullable','in:draft,published'],
        ]);
        $lesson = !empty($data['id']) ? CourseLesson::where('course_id', $course->id)->whereKey($request->input('id'))->firstOrFail() : new CourseLesson();
        if ($lesson->exists) $this->snapshot($course->id, 'lesson', $lesson->id, $lesson->only(['title','description','sort_order','lesson_type','publication_state']));
        $lesson->course_id = $course->id;
        $lesson->title = $data['title'];
        $lesson->description = $data['description'];
        if (Schema::hasColumn('course_lessons','sort_order')) $lesson->sort_order = $data['sort_order'] ?? ($lesson->sort_order ?? 0);
        if (Schema::hasColumn('course_lessons','lesson_type')) $lesson->lesson_type = $data['lesson_type'] ?? 'video';
        if (Schema::hasColumn('course_lessons','publication_state')) $lesson->publication_state = (int) auth()->user()->role === USER_ROLE_INSTRUCTOR ? 'draft' : ($data['publication_state'] ?? 'draft');
        $lesson->save();
        return $this->success([], getMessage($request->filled('id') ? __(UPDATED_SUCCESSFULLY) : __(CREATED_SUCCESSFULLY)));
    }

    public function delete($course_id, $id)
    {
        $course = $this->course($course_id);
        $lesson = CourseLesson::where('course_id', $course->id)->whereKey($id)->firstOrFail();
        $lesson->delete();
        return $this->success([], __(DELETED_SUCCESSFULLY));
    }

    private function snapshot(int $courseId, string $type, int $contentId, array $snapshot): void
    {
        if (!Schema::hasTable('course_content_revisions')) return;
        $revision = DB::table('course_content_revisions')->where('content_type', $type)->where('content_id', $contentId)->max('revision_number') + 1;
        DB::table('course_content_revisions')->insert(['course_id' => $courseId, 'content_type' => $type, 'content_id' => $contentId, 'revision_number' => $revision, 'snapshot' => json_encode($snapshot), 'edited_by' => auth()->id(), 'created_at' => now(), 'updated_at' => now()]);
    }
}
