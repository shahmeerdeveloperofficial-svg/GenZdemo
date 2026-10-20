<?php

namespace App\Http\Controllers\Admin\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseLecture;
use App\Models\CourseLesson;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CourseLectureController extends Controller
{
    use ResponseTrait;

    private function parents($courseId, $lessonId): array
    {
        $courseQuery = Course::whereKey((int) $courseId);
        if ((int) auth()->user()->role === USER_ROLE_INSTRUCTOR) $courseQuery->where('created_by', auth()->id());
        $course = $courseQuery->firstOrFail();
        $lesson = CourseLesson::where('course_id', $course->id)->whereKey($lessonId)->firstOrFail();
        return [$course, $lesson];
    }

    public function store(Request $request, $course_id, $lesson_id)
    {
        [$course, $lesson] = $this->parents($course_id, $lesson_id);
        $data = $request->validate([
            'id' => ['nullable','integer'], 'lecture_title' => ['required','string','min:2','max:195'],
            'sort_order' => ['nullable','integer','min:0','max:100000'], 'publication_state' => ['nullable','in:draft,published'],
            'is_preview' => ['nullable','boolean'],
        ]);
        $lecture = !empty($data['id']) ? CourseLecture::where('course_id', $course->id)->where('course_lesson_id', $lesson->id)->whereKey($data['id'])->firstOrFail() : new CourseLecture();
        if ($lecture->exists && Schema::hasTable('course_content_revisions')) {
            $revision = DB::table('course_content_revisions')->where('content_type','lecture')->where('content_id',$lecture->id)->max('revision_number') + 1;
            DB::table('course_content_revisions')->insert(['course_id'=>$course->id,'content_type'=>'lecture','content_id'=>$lecture->id,'revision_number'=>$revision,'snapshot'=>json_encode($lecture->only(['title','sort_order','publication_state','is_preview'])),'edited_by'=>auth()->id(),'created_at'=>now(),'updated_at'=>now()]);
        }
        $lecture->course_id = $course->id; $lecture->course_lesson_id = $lesson->id; $lecture->title = $data['lecture_title'];
        if (Schema::hasColumn('course_lectures','sort_order')) $lecture->sort_order = $data['sort_order'] ?? ($lecture->sort_order ?? 0);
        if (Schema::hasColumn('course_lectures','publication_state')) $lecture->publication_state = (int) auth()->user()->role === USER_ROLE_INSTRUCTOR ? 'draft' : ($data['publication_state'] ?? 'draft');
        if (Schema::hasColumn('course_lectures','is_preview')) $lecture->is_preview = (bool) ($data['is_preview'] ?? false);
        $lecture->save();
        return $this->success([], getMessage($request->filled('id') ? __(UPDATED_SUCCESSFULLY) : __(CREATED_SUCCESSFULLY)));
    }

    public function delete($course_id, $lesson_id, $id)
    {
        [$course, $lesson] = $this->parents($course_id, $lesson_id);
        CourseLecture::where('course_id', $course->id)->where('course_lesson_id', $lesson->id)->whereKey($id)->firstOrFail()->delete();
        return $this->success([], __(DELETED_SUCCESSFULLY));
    }
}
