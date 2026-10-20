<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AcademicProgram;
use App\Models\AdmissionApplication;
use App\Models\ApplicationDocument;
use App\Models\ApplicationNote;
use App\Models\ProgramIntake;
use App\Models\StudentDocument;
use App\Services\AdmissionsWorkflow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class AdmissionApplicationController extends Controller
{
    public function index(Request $request)
    {
        abort_unless(Schema::hasTable('admission_applications'), 503, 'Admissions migration is not installed on this environment.');
        $applications = AdmissionApplication::where('student_id', $request->user()->id)->with(['university:id,name,slug', 'program:id,title,slug', 'intake:id,label'])->latest()->paginate(15);
        return view('student.crm.applications.index', compact('applications'));
    }

    public function create()
    {
        abort_unless(Schema::hasTable('academic_programs'), 503, 'The study catalog is not available yet.');
        $programs = AcademicProgram::where('status', 'published')->whereHas('university', fn ($q) => $q->where('status', STATUS_ACTIVE))->whereHas('country', fn ($q) => $q->where('status', STATUS_ACTIVE))->with(['university:id,name', 'country:id,name', 'intakes' => fn ($q) => $q->where('status', 'open')])->orderBy('title')->get();
        return view('student.crm.applications.create', compact('programs'));
    }

    public function store(Request $request, AdmissionsWorkflow $workflow)
    {
        $data = $request->validate(['program_id' => ['required', 'integer', 'exists:academic_programs,id'], 'intake_id' => ['nullable', 'integer', 'exists:program_intakes,id']]);
        $program = AcademicProgram::where('status', 'published')->whereHas('university', fn ($q) => $q->where('status', STATUS_ACTIVE))->whereHas('country', fn ($q) => $q->where('status', STATUS_ACTIVE))->findOrFail($data['program_id']);
        $intake = null;
        if (!empty($data['intake_id'])) {
            $intake = ProgramIntake::where('academic_program_id', $program->id)->where('status', 'open')->findOrFail($data['intake_id']);
            if ($intake->application_deadline && $intake->application_deadline->lt(today())) return back()->withErrors(['intake_id' => 'This intake application deadline has passed.'])->withInput();
        }
        $application = $workflow->createForStudent($request->user(), $program, $intake);
        return redirect()->route('student.admissions.show', $application)->with('success', 'Application draft created. Your counselor can help with next steps.');
    }

    public function show(Request $request, AdmissionApplication $application)
    {
        abort_unless((int) $application->student_id === (int) $request->user()->id, 404);
        $application->load(['program', 'university', 'intake', 'history.actor', 'documents.document', 'notes' => fn ($query) => $query->where('visibility', 'student')->with('author')]);
        $documents = StudentDocument::where('user_id', $request->user()->id)->where('status', 'approved')->latest()->get();
        return view('student.crm.applications.show', compact('application', 'documents'));
    }

    public function linkDocument(Request $request, AdmissionApplication $application)
    {
        abort_unless((int) $application->student_id === (int) $request->user()->id, 404);
        abort_unless(in_array($application->status, ['draft','document_collection'], true), 403);
        $data = $request->validate(['document_id' => ['required','integer','exists:student_documents,id']]);
        $document = StudentDocument::where('user_id', $request->user()->id)->where('status', 'approved')->findOrFail($data['document_id']);
        ApplicationDocument::firstOrCreate(['admission_application_id' => $application->id, 'student_document_id' => $document->id], ['linked_by' => $request->user()->id]);
        return back()->with('success', 'Approved private document linked to this application.');
    }

    public function note(Request $request, AdmissionApplication $application)
    {
        abort_unless((int) $application->student_id === (int) $request->user()->id, 404);
        $data = $request->validate(['body' => ['required','string','max:3000']]);
        ApplicationNote::create(['admission_application_id' => $application->id, 'author_id' => $request->user()->id, 'body' => $data['body'], 'visibility' => 'student']);
        return back()->with('success', 'Message added to your application.');
    }
}
