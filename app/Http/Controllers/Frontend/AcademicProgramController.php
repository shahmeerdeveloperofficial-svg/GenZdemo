<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AcademicProgram;
use App\Models\Country;
use App\Models\StudyLevel;
use App\Models\Subject;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;

class AcademicProgramController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'country' => ['nullable', 'integer', 'min:1'],
            'university' => ['nullable', 'integer', 'min:1'],
            'level' => ['nullable', 'integer', 'min:1'],
            'subject' => ['nullable', 'integer', 'min:1'],
            'q' => ['nullable', 'string', 'max:100'],
            'sort' => ['nullable', 'in:title,university,intake'],
        ]);

        if (!Schema::hasTable('academic_programs')) {
            return view('frontend.academic-programs.index', [
                'programs' => new LengthAwarePaginator([], 0, 12, LengthAwarePaginator::resolveCurrentPage(), ['path' => $request->url(), 'query' => $request->query()]),
                'countries' => Country::where('status', STATUS_ACTIVE)->orderBy('name')->get(['id', 'name']),
                'universities' => University::where('status', STATUS_ACTIVE)->orderBy('name')->get(['id', 'name']),
                'levels' => StudyLevel::where('status', STATUS_ACTIVE)->orderBy('name')->get(['id', 'name']),
                'subjects' => Subject::where('status', STATUS_ACTIVE)->orderBy('name')->get(['id', 'name']),
                'filters' => $filters, 'pageTitle' => 'Study Abroad Programs', 'activeStudyAbroadMenu' => 'active', 'bodyClass' => 'bg-white',
            ]);
        }

        $query = AcademicProgram::query()
            ->where('status', 'published')
            ->whereHas('university', fn ($q) => $q->where('status', STATUS_ACTIVE))
            ->whereHas('country', fn ($q) => $q->where('status', STATUS_ACTIVE))
            ->with(['university:id,name,slug,city,country_id', 'country:id,name,slug', 'studyLevel:id,name', 'subject:id,name', 'intakes' => fn ($q) => $q->where('status', 'open')->where(fn ($d) => $d->whereNull('application_deadline')->orWhereDate('application_deadline', '>=', now()->toDateString()))]);

        foreach (['country' => 'country_id', 'university' => 'university_id', 'level' => 'study_level_id', 'subject' => 'subject_id'] as $input => $column) {
            if (!empty($filters[$input])) $query->where($column, $filters[$input]);
        }
        if (!empty($filters['q'])) {
            $term = trim($filters['q']);
            $query->where(fn ($q) => $q->where('title', 'like', "%{$term}%")
                ->orWhereHas('university', fn ($u) => $u->where('name', 'like', "%{$term}%"))
                ->orWhereHas('country', fn ($c) => $c->where('name', 'like', "%{$term}%")));
        }
        match ($filters['sort'] ?? 'title') {
            'university' => $query->orderBy(University::select('name')->whereColumn('universities.id', 'academic_programs.university_id')),
            'intake' => $query->orderByRaw('CASE WHEN EXISTS (SELECT 1 FROM program_intakes pi WHERE pi.academic_program_id = academic_programs.id AND pi.status = ? AND (pi.application_deadline IS NULL OR pi.application_deadline >= ?)) THEN 0 ELSE 1 END', ['open', now()->toDateString()])->orderBy('title'),
            default => $query->orderBy('title'),
        };

        return view('frontend.academic-programs.index', [
            'programs' => $query->paginate(12)->withQueryString(),
            'countries' => Country::where('status', STATUS_ACTIVE)->orderBy('name')->get(['id', 'name']),
            'universities' => University::where('status', STATUS_ACTIVE)->orderBy('name')->get(['id', 'name']),
            'levels' => StudyLevel::where('status', STATUS_ACTIVE)->orderBy('name')->get(['id', 'name']),
            'subjects' => Subject::where('status', STATUS_ACTIVE)->orderBy('name')->get(['id', 'name']),
            'filters' => $filters,
            'pageTitle' => 'Study Abroad Programs',
            'activeStudyAbroadMenu' => 'active',
            'bodyClass' => 'bg-white',
        ]);
    }

    public function show(string $slug)
    {
        abort_unless(Schema::hasTable('academic_programs'), 404);
        $program = AcademicProgram::query()->where('slug', $slug)->where('status', 'published')
            ->whereHas('university', fn ($q) => $q->where('status', STATUS_ACTIVE))
            ->whereHas('country', fn ($q) => $q->where('status', STATUS_ACTIVE))
            ->with(['university:id,name,slug,city,website_url,source_url,country_id', 'country:id,name,slug,source_url', 'studyLevel:id,name', 'subject:id,name', 'intakes' => fn ($q) => $q->where('status', 'open')->where(fn ($d) => $d->whereNull('application_deadline')->orWhereDate('application_deadline', '>=', now()->toDateString()))])
            ->firstOrFail();

        return view('frontend.academic-programs.show', [
            'program' => $program,
            'pageTitle' => $program->meta_title ?: $program->title,
            'activeStudyAbroadMenu' => 'active',
            'bodyClass' => 'bg-white',
        ]);
    }

    public function addToComparison(Request $request)
    {
        $data = $request->validate(['program_id' => ['required', 'integer', 'min:1']]);
        abort_unless(Schema::hasTable('academic_programs') && AcademicProgram::where('status', 'published')->whereKey($data['program_id'])->exists(), 404);
        $ids = collect(session('program_compare', []))->map(fn ($id) => (int) $id)->filter()->unique()->values();
        if (!$ids->contains((int) $data['program_id'])) {
            if ($ids->count() >= 3) return back()->withErrors(['comparison' => 'Compare up to three programs at a time. Remove one to add another.']);
            $ids->push((int) $data['program_id']);
        }
        session(['program_compare' => $ids->all()]);
        return redirect()->route('academic-programs.compare');
    }

    public function compare()
    {
        $programs = collect();
        if (Schema::hasTable('academic_programs')) {
            $ids = collect(session('program_compare', []))->map(fn ($id) => (int) $id)->filter()->unique()->take(3)->all();
            $programs = AcademicProgram::where('status', 'published')->whereIn('id', $ids)
                ->whereHas('university', fn ($q) => $q->where('status', STATUS_ACTIVE))
                ->whereHas('country', fn ($q) => $q->where('status', STATUS_ACTIVE))
                ->with(['university:id,name,city', 'country:id,name', 'studyLevel:id,name', 'intakes' => fn ($q) => $q->where('status', 'open')])->get();
        }
        return view('frontend.academic-programs.compare', ['programs' => $programs, 'pageTitle' => 'Compare Study Programs', 'activeStudyAbroadMenu' => 'active', 'bodyClass' => 'bg-white']);
    }
}
