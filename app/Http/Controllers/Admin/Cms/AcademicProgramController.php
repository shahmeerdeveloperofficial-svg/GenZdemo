<?php

namespace App\Http\Controllers\Admin\Cms;

use App\Http\Controllers\Controller;
use App\Models\AcademicProgram;
use App\Models\Country;
use App\Models\ProgramIntake;
use App\Models\StudyLevel;
use App\Models\Subject;
use App\Models\University;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class AcademicProgramController extends Controller
{
    public function index(Request $request)
    {
        $academicProgramsActive = 'active';
        if (!Schema::hasTable('academic_programs')) {
            $programs = new LengthAwarePaginator([], 0, 20, LengthAwarePaginator::resolveCurrentPage(), ['path' => $request->url(), 'query' => $request->query()]);
            return view('admin.cms.academic-programs.index', compact('programs', 'academicProgramsActive'));
        }
        $programs = AcademicProgram::with(['university:id,name', 'country:id,name', 'studyLevel:id,name'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->input('status')))
            ->when($request->filled('q'), fn ($q) => $q->where('title', 'like', '%'.trim($request->input('q')).'%'))
            ->latest()->paginate(20)->withQueryString();
        return view('admin.cms.academic-programs.index', compact('programs', 'academicProgramsActive'));
    }

    public function importForm()
    {
        abort_unless(Schema::hasTable('academic_programs'), 503, 'Run the academic catalog migration before importing programs.');
        return view('admin.cms.academic-programs.import');
    }

    public function previewImport(Request $request)
    {
        abort_unless(Schema::hasTable('academic_programs'), 503, 'Run the academic catalog migration before importing programs.');
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt', 'max:2048']]);
        $handle = fopen($request->file('file')->getRealPath(), 'rb');
        if (!$handle) return back()->withErrors(['file' => 'Could not read the uploaded CSV.']);
        $headers = fgetcsv($handle, 4096, ',');
        $expected = ['title','slug','university_id','country_id','study_level_id','subject_id','degree_award','duration','description','entry_requirements','source_url','last_verified_at','status'];
        if (!$headers || array_map(fn ($h) => strtolower(trim((string) $h)), $headers) !== $expected) {
            fclose($handle);
            return back()->withErrors(['file' => 'CSV headers do not match the supplied template.']);
        }

        $rows = [];
        $errors = [];
        $seenSlugs = [];
        $line = 1;
        while (($values = fgetcsv($handle, 12000, ',')) !== false) {
            $line++;
            if (count($rows) >= 500) { $errors[] = 'Maximum 500 data rows allowed.'; break; }
            if (count(array_filter($values, fn ($value) => trim((string) $value) !== '')) === 0) continue;
            if (count($values) !== count($expected)) { $errors[] = "Row {$line}: wrong number of columns."; continue; }
            $row = array_combine($expected, array_map(fn ($value) => trim((string) $value), $values));
            $row['entry_requirements'] = $row['entry_requirements'] === '' ? [] : array_values(array_filter(array_map('trim', explode('|', $row['entry_requirements']))));
            $validator = Validator::make($row, [
                'title' => ['required','string','max:255'], 'slug' => ['nullable','string','max:191'],
                'university_id' => ['required','integer','exists:universities,id'], 'country_id' => ['required','integer','exists:countries,id'],
                'study_level_id' => ['nullable','integer','exists:study_levels,id'], 'subject_id' => ['nullable','integer','exists:subjects,id'],
                'degree_award' => ['nullable','string','max:160'], 'duration' => ['nullable','string','max:120'], 'description' => ['nullable','string','max:12000'],
                'entry_requirements' => ['array','max:30'], 'entry_requirements.*' => ['string','max:500'],
                'source_url' => ['nullable','url','max:1000'], 'last_verified_at' => ['nullable','date'], 'status' => ['required', Rule::in(AcademicProgram::STATUSES)],
            ]);
            $rowErrors = $validator->errors()->all();
            if (!$rowErrors) {
                $university = University::find($row['university_id']);
                $country = Country::find($row['country_id']);
                if (!$university || !$country || (int) $university->country_id !== (int) $country->id || (int) $university->status !== STATUS_ACTIVE || (int) $country->status !== STATUS_ACTIVE) $rowErrors[] = 'Selected university and country must be active and match.';
                if ($row['status'] === 'published' && (blank($row['source_url']) || blank($row['last_verified_at']))) $rowErrors[] = 'Published rows need an official source and verification date.';
                $slug = Str::slug($row['slug'] ?: $row['title']);
                if (AcademicProgram::where('slug', $slug)->exists()) $rowErrors[] = 'Slug already exists: '.$slug;
                if (isset($seenSlugs[$slug])) $rowErrors[] = 'Duplicate program slug in this file: '.$slug;
                $seenSlugs[$slug] = true;
            }
            if ($rowErrors) $errors[] = "Row {$line}: ".implode(' ', $rowErrors);
            $row['_line'] = $line;
            $rows[] = $row;
        }
        fclose($handle);
        if (!$rows) $errors[] = 'The CSV contains no data rows.';
        $token = Str::random(48);
        session(['academic_program_import' => ['token' => $token, 'user_id' => $request->user()->id, 'rows' => $rows, 'errors' => $errors]]);
        return view('admin.cms.academic-programs.import-preview', compact('rows', 'errors', 'token'));
    }

    public function confirmImport(Request $request)
    {
        $request->validate(['token' => ['required', 'string', 'size:48']]);
        $batch = session('academic_program_import');
        abort_unless($batch && hash_equals($batch['token'], $request->token) && (int) $batch['user_id'] === (int) $request->user()->id, 403);
        if ($batch['errors']) return back()->withErrors(['import' => 'Correct all preview errors and upload the corrected CSV again.']);
        DB::transaction(function () use ($batch, $request) {
            foreach ($batch['rows'] as $row) {
                $slug = Str::slug($row['slug'] ?: $row['title']);
                AcademicProgram::create([
                    'title' => $row['title'], 'slug' => $slug, 'university_id' => $row['university_id'], 'country_id' => $row['country_id'],
                    'study_level_id' => $row['study_level_id'] ?: null, 'subject_id' => $row['subject_id'] ?: null,
                    'degree_award' => $row['degree_award'] ?: null, 'duration' => $row['duration'] ?: null, 'description' => $row['description'] ?: null,
                    'entry_requirements' => $row['entry_requirements'], 'source_url' => $row['source_url'] ?: null, 'last_verified_at' => $row['last_verified_at'] ?: null,
                    'status' => $row['status'], 'created_by' => $request->user()->id,
                ]);
            }
        });
        $request->session()->forget('academic_program_import');
        return redirect()->route('admin.cms-settings.academic-programs.index')->with('success', count($batch['rows']).' programs imported.');
    }

    public function create()
    {
        abort_unless(Schema::hasTable('academic_programs'), 503, 'Run the academic catalog migration before managing programs.');
        return view('admin.cms.academic-programs.form', $this->formData(new AcademicProgram()) + ['academicProgramsActive' => 'active']);
    }

    public function edit(AcademicProgram $academicProgram)
    {
        abort_unless(Schema::hasTable('academic_programs'), 503, 'Run the academic catalog migration before managing programs.');
        $academicProgram->load('intakes');
        return view('admin.cms.academic-programs.form', $this->formData($academicProgram) + ['academicProgramsActive' => 'active']);
    }

    private function formData(AcademicProgram $program): array
    {
        return [
            'program' => $program,
            'countries' => Country::where('status', STATUS_ACTIVE)->orderBy('name')->get(),
            'universities' => University::where('status', STATUS_ACTIVE)->orderBy('name')->get(),
            'levels' => StudyLevel::where('status', STATUS_ACTIVE)->orderBy('name')->get(),
            'subjects' => Subject::where('status', STATUS_ACTIVE)->orderBy('name')->get(),
        ];
    }

    public function store(Request $request)
    {
        abort_unless(Schema::hasTable('academic_programs'), 503, 'Run the academic catalog migration before managing programs.');
        $validated = $request->validate([
            'id' => ['nullable', 'integer', 'exists:academic_programs,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:191', Rule::unique('academic_programs', 'slug')->ignore($request->input('id'))],
            'university_id' => ['required', 'integer', 'exists:universities,id'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'study_level_id' => ['nullable', 'integer', 'exists:study_levels,id'],
            'subject_id' => ['nullable', 'integer', 'exists:subjects,id'],
            'degree_award' => ['nullable', 'string', 'max:160'],
            'duration' => ['nullable', 'string', 'max:120'],
            'tuition_amount' => ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            'tuition_currency' => ['nullable', 'string', 'size:3'],
            'description' => ['nullable', 'string', 'max:12000'],
            'entry_requirements' => ['nullable', 'array', 'max:30'],
            'entry_requirements.*' => ['nullable', 'string', 'max:500'],
            'source_url' => ['nullable', 'url', 'max:1000'],
            'last_verified_at' => ['nullable', 'date'],
            'status' => ['required', Rule::in(AcademicProgram::STATUSES)],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'intakes' => ['nullable', 'array', 'max:12'],
            'intakes.*.label' => ['nullable', 'string', 'max:120'],
            'intakes.*.start_date' => ['nullable', 'date'],
            'intakes.*.application_deadline' => ['nullable', 'date'],
            'intakes.*.status' => ['nullable', Rule::in(['open', 'closed'])],
            'intakes.*.source_url' => ['nullable', 'url', 'max:1000'],
        ]);

        if ($validated['status'] === 'published' && (empty($validated['source_url']) || empty($validated['last_verified_at']))) {
            return back()->withErrors(['source_url' => 'A published program needs an official source URL and a verification date.'])->withInput();
        }

        $university = University::where('status', STATUS_ACTIVE)->findOrFail($validated['university_id']);
        $country = Country::where('status', STATUS_ACTIVE)->findOrFail($validated['country_id']);
        if ((int) $university->country_id !== (int) $country->id) {
            return back()->withErrors(['country_id' => 'Choose the country that belongs to the selected university.'])->withInput();
        }

        DB::transaction(function () use ($validated, $request) {
            $program = !empty($validated['id']) ? AcademicProgram::findOrFail($validated['id']) : new AcademicProgram();
            $program->fill(collect($validated)->except(['id', 'intakes'])->all());
            $program->tuition_currency = strtoupper($program->tuition_currency ?? '');
            $program->slug = Str::slug($validated['slug'] ?: $validated['title']);
            $program->entry_requirements = array_values(array_filter($validated['entry_requirements'] ?? [], fn ($value) => filled($value)));
            $program->created_by ??= $request->user()->id;
            $program->save();

            $program->intakes()->delete();
            foreach ($validated['intakes'] ?? [] as $intake) {
                if (blank($intake['label'] ?? null)) continue;
                $program->intakes()->create([
                    'label' => $intake['label'],
                    'start_date' => $intake['start_date'] ?? null,
                    'application_deadline' => $intake['application_deadline'] ?? null,
                    'status' => $intake['status'] ?? 'open',
                    'source_url' => $intake['source_url'] ?? null,
                    'last_verified_at' => $program->last_verified_at,
                ]);
            }
        });

        return redirect()->route('admin.cms-settings.academic-programs.index')->with('success', 'Academic program saved.');
    }

    public function destroy(AcademicProgram $academicProgram)
    {
        abort_unless(Schema::hasTable('academic_programs'), 503, 'Run the academic catalog migration before managing programs.');
        $academicProgram->delete();
        return redirect()->route('admin.cms-settings.academic-programs.index')->with('success', 'Academic program archived.');
    }
}
