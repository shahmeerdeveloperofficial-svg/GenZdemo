<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\StudentDocument;
use App\Models\StudentProfile;
use App\Models\Subject;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StudentProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $profile = StudentProfile::firstOrNew(['user_id' => $user->id]);
        $documents = StudentDocument::where('user_id', $user->id)->orderByDesc('id')->get();
        $completionItems = [
            __('Name and account email') => filled($user->first_name) && filled($user->email),
            __('Date of birth') => filled($profile->date_of_birth),
            __('Gender') => filled($profile->gender),
            __('Nationality') => filled($profile->nationality),
            __('Present address') => filled($profile->present_address),
            __('At least one destination') => !empty($profile->preferred_country_ids),
            __('Target intake') => filled($profile->target_intake),
            __('At least one document uploaded') => $documents->where('status', '!=', 'superseded')->isNotEmpty(),
        ];
        $completion = (int) round(count(array_filter($completionItems)) / count($completionItems) * 100);

        return view('student.crm.profile', [
            'profile' => $profile, 'documents' => $documents, 'completionItems' => $completionItems, 'completion' => $completion,
            'countries' => Country::where('status', STATUS_ACTIVE)->orderBy('name')->get(['id', 'name']),
            'universities' => University::where('status', STATUS_ACTIVE)->orderBy('name')->get(['id', 'name', 'country_id']),
            'subjects' => Subject::where('status', STATUS_ACTIVE)->orderBy('name')->get(['id', 'name', 'university_id']),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'gender' => ['nullable', Rule::in(['female', 'male', 'non_binary', 'prefer_not_to_say'])],
            'nationality' => ['nullable', 'string', 'max:120'],
            'passport_number' => ['nullable', 'string', 'max:80'],
            'present_address' => ['nullable', 'string', 'max:2000'],
            'permanent_address' => ['nullable', 'string', 'max:2000'],
            'preferred_country_ids' => ['nullable', 'array', 'max:10'],
            'preferred_country_ids.*' => ['integer', Rule::exists('countries', 'id')->where('status', STATUS_ACTIVE)],
            'preferred_university_ids' => ['nullable', 'array', 'max:20'],
            'preferred_university_ids.*' => ['integer', Rule::exists('universities', 'id')->where('status', STATUS_ACTIVE)],
            'preferred_subject_ids' => ['nullable', 'array', 'max:20'],
            'preferred_subject_ids.*' => ['integer', Rule::exists('subjects', 'id')->where('status', STATUS_ACTIVE)],
            'target_intake' => ['nullable', 'string', 'max:120'],
        ]);
        $data['user_id'] = auth()->id();
        $data['preferred_country_ids'] = array_values(array_unique($data['preferred_country_ids'] ?? []));
        $data['preferred_university_ids'] = array_values(array_unique($data['preferred_university_ids'] ?? []));
        $data['preferred_subject_ids'] = array_values(array_unique($data['preferred_subject_ids'] ?? []));
        StudentProfile::updateOrCreate(['user_id' => auth()->id()], $data);
        return back()->with('success', __('Your study profile was saved.'));
    }
}
