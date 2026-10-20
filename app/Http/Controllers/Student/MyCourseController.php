<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseEnrollment;
use App\Models\CourseLectureFinish;
use App\Models\CourseResource;
use App\Models\CourseLiveSession;
use App\Models\CourseBatchMembership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MyCourseController extends Controller
{
    public function list(Request $request)
    {
        $data['pageTitle'] = __('My Course');
        $data['activeMyCourse'] = 'active';
        $query = CourseEnrollment::join('courses', 'course_enrollments.course_id', '=', 'courses.id')
            ->join('programs', 'courses.program_id', '=', 'programs.id')
            ->where('course_enrollments.status', STATUS_ACTIVE)
            ->where('course_enrollments.user_id', auth()->id())
            ->select(['programs.title as program_title', 'courses.id as course_id', 'courses.title', 'courses.program_id', 'courses.thumbnail', 'course_enrollments.id']);
        if (Schema::hasColumn('course_enrollments','access_expires_at')) $query->where(fn ($q) => $q->whereNull('course_enrollments.access_expires_at')->orWhere('course_enrollments.access_expires_at','>',now()));
        if ($request->filled('search_key')) $query->where('courses.title', 'like', '%'.trim($request->search_key).'%');
        $data['myCourses'] = $query->orderByDesc('course_enrollments.id')->paginate(12);
        if ($request->ajax()) return view('student.my_courses.partials.course-list', $data)->render();
        return view('student.my_courses.list', $data);
    }

    public function view(Request $request, $enrollmentId)
    {
        $enrollment = app(\App\Services\LmsEnrollmentService::class)->activeEnrollment(decodeId($enrollmentId), (int) auth()->id());
        $courseQuery = Course::whereKey($enrollment->course_id);
        $published = fn ($query) => Schema::hasColumn($query->getModel()->getTable(), 'publication_state')
            ? $query->where('publication_state', 'published') : $query;
        $course = $courseQuery->with(['lessons' => function ($lessons) use ($published) {
            $published($lessons)->with(['lectures' => function ($lectures) use ($published) {
                $published($lectures)->with(['resources' => fn ($resources) => $published($resources)]);
            }]);
        }])->firstOrFail();
        $course->enrollment_id = $enrollment->id;
        $liveSessions = collect();
        if (Schema::hasTable('course_live_sessions')) {
            $liveSessions = CourseLiveSession::where('course_id',$course->id)->where('status','scheduled')
                ->where('starts_at','>=',now()->subDays(7))
                ->where(fn ($q) => $q->whereNull('course_batch_id')->orWhereIn('course_batch_id',CourseBatchMembership::where('course_enrollment_id',$enrollment->id)->select('course_batch_id')))
                ->with(['attendance'=>fn ($q) => $q->where('course_enrollment_id',$enrollment->id)])
                ->orderBy('starts_at')->limit(20)->get();
        }

        $requestedResourceId = $request->filled('resource_id') ? decodeId($request->input('resource_id')) : null;
        $resourceQuery = $this->accessibleResources($course->id);
        $resource = $requestedResourceId ? (clone $resourceQuery)->whereKey($requestedResourceId)->first() : null;
        // A forged resource ID never escapes its active enrollment/course scope.
        abort_if($requestedResourceId && !$resource, 404);
        $resource ??= (clone $resourceQuery)->orderBy('sort_order')->orderBy('id')->first();

        $data = [
            'pageTitle' => __('My Course Details'), 'activeMyCourse' => 'active', 'course' => $course,
            'currentResource' => $resource, 'currentResourceId' => $resource?->id,
            'currentLessonId' => $resource?->lesson?->id, 'currentLectureId' => $resource?->lecture?->id,
            'finishedResourceIds' => CourseLectureFinish::where('enrollment_id', $enrollment->id)->where('user_id', auth()->id())->pluck('course_resource_id')->all(),
            'progressPercent' => app(\App\Services\LmsEnrollmentService::class)->completionPercent($enrollment),
            'currentProgress' => Schema::hasTable('lms_lesson_progress') && $resource ? \App\Models\LmsLessonProgress::where('course_enrollment_id',$enrollment->id)->where('course_resource_id',$resource->id)->first() : null,
            'liveSessions' => $liveSessions,
        ];
        return view('student.my_courses.details', $data);
    }

    public function completeResource(Request $request, $enrollmentId, $resourceId)
    {
        $enrollment = app(\App\Services\LmsEnrollmentService::class)->activeEnrollment(decodeId($enrollmentId), (int) auth()->id());
        $resourceId = decodeId($resourceId);
        $resourceQuery = $this->accessibleResources($enrollment->course_id)->whereKey($resourceId);
        $resource = $resourceQuery->firstOrFail();

        app(\App\Services\LmsEnrollmentService::class)->recordProgress($enrollment, $resource, (int) $request->input('last_position_seconds',0), 0, true);
        return response()->json(['ok' => true, 'completed' => true]);
    }

    public function saveProgress(Request $request, $enrollmentId, $resourceId)
    {
        abort_unless(Schema::hasTable('lms_lesson_progress'), 503, 'Apply the LMS progress migration before saving resumable progress.');
        $data = $request->validate(['last_position_seconds'=>['required','integer','min:0','max:86400'],'time_spent_seconds'=>['nullable','integer','min:0','max:120'],'completed'=>['nullable','boolean']]);
        $enrollment = app(\App\Services\LmsEnrollmentService::class)->activeEnrollment(decodeId($enrollmentId), (int) auth()->id());
        $resource = $this->accessibleResources($enrollment->course_id)->whereKey(decodeId($resourceId))->firstOrFail();
        $progress = app(\App\Services\LmsEnrollmentService::class)->recordProgress($enrollment, $resource, $data['last_position_seconds'], $data['time_spent_seconds'] ?? 0, (bool) ($data['completed'] ?? false));
        return response()->json(['ok'=>true,'last_position_seconds'=>$progress->last_position_seconds,'completed'=>(bool) $progress->completed_at]);
    }

    private function accessibleResources(int $courseId)
    {
        $query = CourseResource::where('course_id', $courseId);
        if (Schema::hasColumn('course_resources', 'publication_state')) $query->where('publication_state', 'published');
        return $query->whereHas('lecture', function ($lecture) use ($courseId) {
            $lecture->where('course_id', $courseId);
            if (Schema::hasColumn('course_lectures', 'publication_state')) $lecture->where('publication_state', 'published');
            $lecture->whereHas('lesson', function ($lesson) use ($courseId) {
                $lesson->where('course_id', $courseId);
                if (Schema::hasColumn('course_lessons', 'publication_state')) $lesson->where('publication_state', 'published');
            });
        });
    }
}
