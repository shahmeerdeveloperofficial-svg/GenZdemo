<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmissionApplication;
use App\Models\ApplicationNote;
use App\Models\ApplicationTask;
use App\Models\User;
use App\Services\AdmissionsWorkflow;
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
        $applications = $this->visibleApplications()->with(['student:id,first_name,last_name,email', 'university:id,name', 'program:id,title'])->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))->latest()->paginate(25)->withQueryString();
        $routePrefix = (int) auth()->user()->role === USER_ROLE_CONSULTANT ? 'consultant.admissions' : 'admin.admissions';
        $documentRoutePrefix = (int) auth()->user()->role === USER_ROLE_CONSULTANT ? 'consultant.student_documents' : 'admin.student_documents';
        return view('admin.crm.admissions.index', compact('applications', 'routePrefix'));
    }

    public function show(Request $request, AdmissionApplication $application)
    {
        abort_unless($this->visibleApplications()->whereKey($application->id)->exists(), 404);
        $application->load(['student', 'university', 'program', 'intake', 'history.actor', 'documents.document', 'notes.author', 'tasks']);
        $routePrefix = (int) auth()->user()->role === USER_ROLE_CONSULTANT ? 'consultant.admissions' : 'admin.admissions';
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
