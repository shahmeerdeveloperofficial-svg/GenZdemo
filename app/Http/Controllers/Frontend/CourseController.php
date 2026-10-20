<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Faq;
use App\Models\Program;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class CourseController extends Controller
{
    public function list(Request $request)
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100']]);
        $courses = $this->publishedCourses()->with('program:id,title,slug')
            ->when(!empty($filters['q']), fn ($query) => $query->where('title', 'like', '%'.trim($filters['q']).'%'))
            ->orderBy('title')->paginate(12)->withQueryString();
        return view('frontend.courses.list', ['courses' => $courses, 'filters' => $filters, 'pageTitle' => 'Online Learning', 'activeProgramMenu' => 'active', 'bodyClass' => 'bg-white']);
    }

    public function courseProgram($slug)
    {
        $program = Program::where('status', STATUS_ACTIVE)->where('slug', $slug)->firstOrFail();
        $data['pageTitle'] = __($program->title);
        $data['program'] = $program;
        $data['testimonialData'] = Testimonial::where('status',STATUS_ACTIVE)->get();
        $data['faqData'] = Faq::where('status',STATUS_ACTIVE)->get();
        $data['activeCourseProgram'.$data['program']->id] = 'active';
        $data['activeProgramMenu'] = 'active';
        $data['bodyClass'] = 'bg-white';
        $data['courses'] = $this->publishedCourses()->where('program_id', $program->id)->withCount(['lessons' => fn ($query) => $this->publishedContent($query, 'course_lessons')])->get();
        return view('frontend.courses.programs', $data);
    }

    public function course($slug)
    {
        $course = $this->publishedCourses()
            ->with(['program', 'lessons' => function ($lessons) {
                $this->publishedContent($lessons, 'course_lessons')->with(['lectures' => function ($lectures) {
                    $this->publishedContent($lectures, 'course_lectures')->with(['resources' => fn ($resources) => $this->publishedContent($resources, 'course_resources')]);
                }]);
            }])
            ->where('slug', $slug)
            ->firstOrFail();
        $data['pageTitle'] = __($course->title);
        $data['activeCourseProgram'.$course->program_id] = 'active';
        $data['activeProgramMenu'] = 'active';
        $data['bodyClass'] = 'bg-white';
        $data['course'] = $course;

        return view('frontend.courses.single-course', $data);
    }

    public function video($slug){

        $data['resource'] = $this->publishedCourses()->where('slug',$slug)->firstOrFail();
        return view('frontend.courses.resource-view', $data);
    }

    private function publishedCourses()
    {
        return Course::publiclyVisible();
    }

    private function publishedContent($query, string $table)
    {
        return Schema::hasColumn($table, 'publication_state') ? $query->where('publication_state', 'published') : $query;
    }

}
