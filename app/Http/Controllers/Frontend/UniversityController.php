<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Faq;
use App\Models\Subject;
use App\Models\University;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    public function list(Request $request)
    {
        $filters = $request->validate(['country' => ['nullable', 'array'], 'country.*' => ['integer'], 'university' => ['nullable', 'array'], 'university.*' => ['integer'], 'search_key' => ['nullable', 'string', 'max:100'], 'sort_by' => ['nullable', 'in:name-asc,name-desc,ranking-asc,ranking-desc']]);
        $query = University::query()->where('universities.status', STATUS_ACTIVE)->with('country:id,name');
        if (!empty($filters['country'])) $query->whereIn('country_id', $filters['country']);
        if (!empty($filters['university'])) $query->whereIn('id', $filters['university']);
        if (!empty($filters['search_key'])) {
            $term = trim($filters['search_key']);
            $query->where(fn ($q) => $q->where('name', 'like', "%{$term}%")->orWhere('details', 'like', "%{$term}%"));
        }
        $sort = $filters['sort_by'] ?? 'name-asc';
        [$column, $direction] = explode('-', $sort);
        $query->orderBy($column === 'ranking' ? 'world_ranking' : 'name', $direction);

        // Paginate the filtered/sorted results
        $data['universityData'] = $query->paginate(12)->appends($request->query());

        // Other data
        $data['countries'] = Country::where('status', STATUS_ACTIVE)->get();
        $data['pageTitle'] = __('Universities');
        $data['activeUniversityMenu'] = 'active';
        $data['activeStudyAbroadMenu'] = 'active';
        $data['bodyClass'] = 'bg-white';

        // Return the view with data
        return view('frontend.universities.list', $data);
    }

    public function details($slug){

        $data['activeUniversityMenu'] = 'active';
        $data['activeStudyAbroadMenu'] = 'active';
        $data['bodyClass'] = 'bg-white';
        $data['universityData'] = University::where('universities.status', STATUS_ACTIVE)->leftjoin('countries','universities.country_id','=','countries.id')
            ->select('universities.*','countries.name as countryName')
            ->where('universities.slug',$slug)->firstOrFail();
        $data['faqData'] = Faq::where('status',STATUS_ACTIVE)->get();
        $data['subjectData'] = Subject::leftjoin('universities','subjects.university_id','=','universities.id')
             ->leftjoin('countries','subjects.country_id','=','countries.id')
             ->leftjoin('study_levels','subjects.study_level_id','=','study_levels.id')
             ->leftjoin('subject_categories','subjects.subject_category_id','=','subject_categories.id')
             ->select('subjects.name as subjectName',
                      'subjects.slug as subjectSlug',
                      'subjects.banner_image',
                      'subjects.intake_time as subjectIntakeTime',
                      'countries.name as countryName',
                      'study_levels.name as studyLevelName',
                      'subject_categories.name as subjectCategoryName')
             ->where('subjects.status', STATUS_ACTIVE)->where('universities.status', STATUS_ACTIVE)->where('universities.slug', $slug)
             ->get();
        return view('frontend.universities.details', $data);
    }
}
