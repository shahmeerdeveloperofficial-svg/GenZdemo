<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionApplication;
use App\Models\ApplicationNote;
use App\Models\ApplicationTask;
use App\Models\User;
use App\Services\AdmissionsWorkflow;
use App\Support\ListCriteria;
use App\Models\University;
use App\Models\AcademicProgram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AdmissionApplicationController extends Controller
{
    private function visibleApplications()
    {
        $query = AdmissionApplication::query();
        if ((int) auth()->user()->role === USER_ROLE_CONSULTANT) $query->where('counselor_id', auth()->id());
        return $query;
    }

    public function index(Request $request)
    {
        abort_unless(Schema::hasTable('admission_applications'), 503, 'Admissions migration is not installed on this environment.');
        $request->validate([
            'status' => ['nullable', Rule::in(AdmissionApplication::STATUSES)],
            'visa_status' => ['nullable', Rule::in(AdmissionApplication::VISA_STATUSES)],
            'branch' => ['nullable', Rule::in(array_keys(ListCriteria::branchOptions()))],
            'university_id' => ['nullable', 'integer'], 'academic_program_id' => ['nullable', 'integer'],
            'counselor_id' => ['nullable', 'integer'],
            'period' => ['nullable', Rule::in(array_keys(ListCriteria::periods()))],
        ]);
        $query = $this->visibleApplications()->with(['student:id,first_name,last_name,email,branch', 'university:id,name', 'program:id,title']);
        foreach (['status', 'visa_status', 'university_id', 'academic_program_id', 'counselor_id'] as $field) {
            if ($request->filled($field)) $query->where($field, $request->input($field));
        }
        if ($request->filled('branch')) {
            $query->whereHas('student', fn ($q) => ListCriteria::applyBranch($q, 'users.branch', $request->input('branch')));
        }
        ListCriteria::applyPeriod($query, 'created_at', $request->input('period'));
        $applications = $query->latest()->paginate(25)->withQueryString();
        $criteriaFields = [
            ['name' => 'branch', 'label' => __('Branch'), 'options' => ListCriteria::branchOptions()],
            ['name' => 'status', 'label' => __('Stage'), 'options' => collect(AdmissionApplication::STATUSES)->mapWithKeys(fn ($v) => [$v => __(str_replace('_', ' ', ucfirst($v)))])->all()],
            ['name' => 'visa_status', 'label' => __('Visa status'), 'options' => collect(AdmissionApplication::VISA_STATUSES)->mapWithKeys(fn ($v) => [$v => __(str_replace('_', ' ', ucfirst($v)))])->all()],
            ['name' => 'university_id', 'label' => __('University'), 'options' => University::whereIn('id', $this->visibleApplications()->select('university_id'))->orderBy('name')->pluck('name', 'id')->all()],
            ['name' => 'academic_program_id', 'label' => __('Program'), 'options' => AcademicProgram::whereIn('id', $this->visibleApplications()->select('academic_program_id'))->orderBy('title')->pluck('title', 'id')->all()],
            ['name' => 'counselor_id', 'label' => __('Counselor'), 'options' => User::whereIn('id', $this->visibleApplications()->select('counselor_id'))->orderBy('first_name')->get()->mapWithKeys(fn ($user) => [$user->id => $user->name])->all()],
            ['name' => 'period', 'label' => __('Created'), 'options' => ListCriteria::periods()],
        ];
        $routePrefix = (int) auth()->user()->role === USER_ROLE_CONSULTANT ? 'consultant.admissions' : 'admin.admissions';
        $documentRoutePrefix = (int) auth()->user()->role === USER_ROLE_CONSULTANT ? 'consultant.student_documents' : 'admin.student_documents';
        return view('admin.crm.admissions.index', compact('applications', 'routePrefix', 'criteriaFields'));
    }

    public function show(Request $request, AdmissionApplication $application)
    {
        abort_unless($this->visibleApplications()->whereKey($application->id)->exists(), 404);
        $application->load(['student', 'university', 'program', 'intake', 'history.actor', 'documents.document', 'notes.author', 'tasks']);
        $routePrefix = (int) auth()->user()->role === USER_ROLE_CONSULTANT ? 'consultant.admissions' : 'admin.admissions';
        $documentRoutePrefix = (int) auth()->user()->role === USER_ROLE_CONSULTANT ? 'consultant.student_documents' : 'admin.student_documents';
        $consultants = User::where('role', USER_ROLE_CONSULTANT)->where('status', STATUS_ACTIVE)->orderBy('first_name')->get(['id','first_name','last_name']);
        return view('admin.crm.admissions.show', compact('application', 'routePrefix', 'documentRoutePrefix', 'consultants'));
    }

    public function transition(Request $request, AdmissionApplication $application, AdmissionsWorkflow $workflow)
    {
        abort_unless($this->visibleApplications()->whereKey($application->id)->exists(), 404);
        $data = $request->validate(['status' => ['required', Rule::in(AdmissionApplication::STATUSES)], 'reason' => ['nullable','string','max:2000']]);
        $workflow->transition($application, $data['status'], $request->user(), $data['reason'] ?? null);
        return back()->with('success', 'Application status updated and recorded in its timeline.');
    }

    public function assign(Request $request, AdmissionApplication $application)
    {
        abort_unless((int) $request->user()->role !== USER_ROLE_CONSULTANT, 403);
        $application = $this->visibleApplications()->whereKey($application->id)->firstOrFail();
        $data = $request->validate(['counselor_id' => ['nullable','integer', Rule::exists('users','id')->where('role', USER_ROLE_CONSULTANT)->where('status', STATUS_ACTIVE)]]);
        $application->update(['counselor_id' => $data['counselor_id'] ?? null]);
        return back()->with('success', 'Counselor assignment updated.');
    }

    public function visa(Request $request, AdmissionApplication $application)
    {
        abort_unless($this->visibleApplications()->whereKey($application->id)->exists(), 404);
        $data = $request->validate(['visa_status' => ['required', Rule::in(AdmissionApplication::VISA_STATUSES)]]);
        $application->update(['visa_status' => $data['visa_status']]);
        return back()->with('success', 'Visa milestone updated.');
    }

    public function note(Request $request, AdmissionApplication $application)
    {
        abort_unless($this->visibleApplications()->whereKey($application->id)->exists(), 404);
        $data = $request->validate(['body' => ['required','string','max:5000'], 'visibility' => ['required', Rule::in(['internal','student'])]]);
        ApplicationNote::create(['admission_application_id' => $application->id, 'author_id' => $request->user()->id, ...$data]);
        return back()->with('success', 'Application note added.');
    }

    public function task(Request $request, AdmissionApplication $application)
    {
        abort_unless($this->visibleApplications()->whereKey($application->id)->exists(), 404);
        $data = $request->validate(['title' => ['required','string','max:180'], 'description' => ['nullable','string','max:3000'], 'assignee_id' => ['nullable','integer', Rule::exists('users','id')->where('status', STATUS_ACTIVE)], 'due_at' => ['nullable','date']]);
        ApplicationTask::create(['admission_application_id' => $application->id, 'created_by' => $request->user()->id, ...$data]);
        return back()->with('success', 'Application task created.');
    }

    public function completeTask(Request $request, ApplicationTask $task)
    {
        $application = $this->visibleApplications()->whereKey($task->admission_application_id)->firstOrFail();
        abort_unless((int) $request->user()->role !== USER_ROLE_CONSULTANT || (int) $task->assignee_id === (int) $request->user()->id || (int) $task->created_by === (int) $request->user()->id, 404);
        $task->update(['completed_at' => $task->completed_at ?: now()]);
        return back()->with('success', 'Application task marked complete.');
    }
}
