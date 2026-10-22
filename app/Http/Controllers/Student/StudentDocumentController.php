<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\StudentDocument;
use App\Models\FileManager;
use App\Models\User;
use App\Models\StudentProfile;
use App\Models\StudentServiceOrder;
use App\Models\AdmissionApplication;
use App\Support\ListCriteria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class StudentDocumentController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => ['required', Rule::in(StudentDocument::CATEGORIES)],
            'document' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'mimetypes:application/pdf,image/jpeg,image/png', 'max:10240'],
        ]);
        $file = $data['document'];
        $previous = StudentDocument::where('user_id', auth()->id())->where('category', $data['category'])->where('status', '!=', 'superseded')->latest('id')->first();
        $previousWasPending = $previous?->status === 'pending';
        $path = 'student-documents/' . auth()->id() . '/' . \Illuminate\Support\Str::uuid() . '.enc';

        try {
            Storage::disk('private')->put($path, Crypt::encryptString(file_get_contents($file->getRealPath())));
            DB::transaction(function () use ($previous, $previousWasPending, $data, $path, $file) {
                if ($previous) $previous->update([
                    'status' => 'superseded',
                    'review_note' => $previousWasPending ? __('Superseded by a resubmission.') : $previous->review_note,
                ]);
                StudentDocument::create([
                    'user_id' => auth()->id(), 'replaces_document_id' => $previous?->id,
                    'category' => $data['category'], 'storage_path' => $path,
                    'original_name' => basename($file->getClientOriginalName()), 'mime_type' => $file->getMimeType(),
                    'size_bytes' => $file->getSize(), 'status' => 'pending',
                ]);
            });
            return back()->with('success', __('Document uploaded securely and queued for review.'));
        } catch (\Throwable $exception) {
            Storage::disk('private')->delete($path);
            report($exception);
            return back()->withErrors(['document' => __('The document could not be saved. Please retry.')]);
        }
    }

    public function download(StudentDocument $document)
    {
        abort_unless($this->canAccessStudent($document->user_id), 404);
        abort_unless(Storage::disk('private')->exists($document->storage_path), 404);
        try {
            $contents = Crypt::decryptString(Storage::disk('private')->get($document->storage_path));
        } catch (\Throwable $exception) {
            report($exception);
            abort(404);
        }
        return response($contents, 200, [
            'Content-Type' => $document->mime_type,
            'Content-Disposition' => 'attachment; filename="' . str_replace(['"', "\r", "\n"], '', $document->original_name) . '"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }

    public function downloadLegacyFile(FileManager $file)
    {
        abort_unless($file->storage_type === 'private' && $file->user_id, 404);
        abort_unless($this->canAccessStudent((int) $file->user_id), 404);
        abort_unless(Storage::disk('private')->exists($file->path), 404);
        try {
            $contents = Crypt::decryptString(Storage::disk('private')->get($file->path));
        } catch (\Throwable $exception) {
            report($exception);
            abort(404);
        }
        return response($contents, 200, [
            'Content-Type' => $file->file_type ?: 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . str_replace(['"', "\r", "\n"], '', basename($file->original_name)) . '"',
            'X-Content-Type-Options' => 'nosniff', 'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }

    private function visibleDocuments()
    {
        $query = StudentDocument::query();
        if ((int) auth()->user()->role === USER_ROLE_CONSULTANT) {
            $assignedStudentIds = StudentServiceOrder::whereHas('assignees', fn ($query) => $query->where('assigned_to', auth()->id())->where('is_active', STATUS_ACTIVE))->pluck('student_id');
            $admissionStudentIds = AdmissionApplication::where('counselor_id', auth()->id())->pluck('student_id');
            $query->whereIn('user_id', $assignedStudentIds->merge($admissionStudentIds)->unique());
        }
        return $query;
    }

    public function reviewQueue(Request $request)
    {
        $request->validate([
            'status' => ['nullable', Rule::in(StudentDocument::STATUSES)],
            'category' => ['nullable', Rule::in(StudentDocument::CATEGORIES)],
            'student_id' => ['nullable', 'integer'],
            'branch' => ['nullable', Rule::in(array_keys(ListCriteria::branchOptions()))],
            'period' => ['nullable', Rule::in(array_keys(ListCriteria::periods()))],
        ]);
        $query = $this->visibleDocuments()->with('student');
        // A first visit shows pending reviews. Selecting "All" includes history.
        $status = $request->input('status', 'pending');
        if (filled($status)) $query->where('status', $status);
        if ($request->filled('category')) $query->where('category', $request->input('category'));
        if ($request->filled('student_id')) $query->where('user_id', $request->integer('student_id'));
        if ($request->filled('branch')) {
            $query->whereHas('student', fn ($q) => ListCriteria::applyBranch($q, 'users.branch', $request->input('branch')));
        }
        ListCriteria::applyPeriod($query, 'created_at', $request->input('period'));
        $documents = $query->latest()->paginate(25)->withQueryString();
        $students = User::whereIn('id', $this->visibleDocuments()->select('user_id'))
            ->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'email']);
        $criteriaFields = [
            ['name' => 'branch', 'label' => __('Branch'), 'options' => ListCriteria::branchOptions()],
            ['name' => 'category', 'label' => __('Document type'), 'options' => collect(StudentDocument::CATEGORIES)->mapWithKeys(fn ($value) => [$value => __(str_replace('_', ' ', ucfirst($value)))])->all()],
            ['name' => 'status', 'label' => __('Review status'), 'default' => 'pending', 'options' => collect(StudentDocument::STATUSES)->mapWithKeys(fn ($value) => [$value => __(ucfirst($value))])->all()],
            ['name' => 'student_id', 'label' => __('Student'), 'options' => $students->mapWithKeys(fn ($student) => [$student->id => $student->name . ' (' . $student->email . ')'])->all()],
            ['name' => 'period', 'label' => __('Uploaded'), 'options' => ListCriteria::periods()],
        ];
        $routePrefix = (int) auth()->user()->role === USER_ROLE_CONSULTANT ? 'consultant.student_documents' : 'admin.student_documents';
        return view('admin.crm.student-documents.index', compact('documents', 'routePrefix', 'criteriaFields'));
    }

    public function studentProfile(User $student)
    {
        abort_unless((int) $student->role === USER_ROLE_STUDENT && $this->canAccessStudent((int) $student->id), 404);
        $profile = StudentProfile::where('user_id', $student->id)->first();
        $documents = StudentDocument::where('user_id', $student->id)->orderByDesc('id')->get();
        $routePrefix = (int) auth()->user()->role === USER_ROLE_CONSULTANT ? 'consultant.student_documents' : 'admin.student_documents';
        return view('admin.crm.student-documents.profile', compact('student', 'profile', 'documents', 'routePrefix'));
    }

    public function review(Request $request, StudentDocument $document)
    {
        abort_unless($this->canAccessStudent($document->user_id), 404);
        abort_unless($document->status === 'pending', 409);
        $data = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'review_note' => ['nullable', 'string', 'max:2000', Rule::requiredIf(fn () => $request->input('status') === 'rejected')],
        ]);
        $updated = StudentDocument::whereKey($document->id)->where('status', 'pending')->update([
            'status' => $data['status'], 'review_note' => $data['review_note'] ?? null,
            'reviewed_by' => auth()->id(), 'reviewed_at' => now(), 'updated_at' => now(),
        ]);
        abort_unless($updated === 1, 409);
        return back()->with('success', __('Document review saved.'));
    }

    private function canAccessStudent(int $studentId): bool
    {
        $role = (int) auth()->user()->role;
        if ($role === USER_ROLE_STUDENT) return (int) auth()->id() === $studentId;
        if (in_array($role, [USER_ROLE_ADMIN, USER_ROLE_STAFF], true)) return auth()->user()->can('Manage Students');
        if ($role === USER_ROLE_CONSULTANT) {
            return StudentServiceOrder::where('student_id', $studentId)
                ->whereHas('assignees', fn ($query) => $query->where('assigned_to', auth()->id())->where('is_active', STATUS_ACTIVE))
                ->exists() || (Schema::hasTable('admission_applications') && AdmissionApplication::where('student_id', $studentId)->where('counselor_id', auth()->id())->exists());
        }
        return false;
    }
}
