<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CrmFollowUp;
use App\Models\CrmLead;
use App\Models\CrmLeadActivity;
use App\Models\CrmLeadNote;
use App\Models\StudyAbroadRegistration;
use App\Models\CrmFollowUpComment;
use App\Services\CrmTaskNotificationOutbox;
use App\Support\ListCriteria;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    private function visibleLeads()
    {
        $query = CrmLead::query();
        if (in_array((int) auth()->user()->role, [USER_ROLE_STAFF, USER_ROLE_CONSULTANT], true)) {
            $query->where(function ($scope) {
                $scope->where('assigned_to', auth()->id());
                if ((int) auth()->user()->role === USER_ROLE_STAFF) $scope->orWhere('created_by', auth()->id());
            });
        }
        return $query;
    }

    public function index(Request $request)
    {
        $request->validate([
            'assigned_to' => ['nullable', 'integer'],
            'branch' => ['nullable', Rule::in(array_keys(ListCriteria::branchOptions()))],
            'period' => ['nullable', Rule::in(array_keys(ListCriteria::periods()))],
        ]);
        $query = $this->visibleLeads()->with('owner');
        if ($request->filled('q')) {
            $term = trim($request->string('q')->toString());
            $query->where(function ($filter) use ($term) {
                $filter->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhere('email', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%");
            });
        }
        if ($request->filled('status') && in_array($request->status, CrmLead::STATUSES, true)) $query->where('status', $request->status);
        if ($request->filled('source')) $query->where('source', $request->string('source')->toString());
        if ($request->filled('assigned_to')) $query->where('assigned_to', $request->integer('assigned_to'));
        if ($request->filled('branch')) {
            if ($request->input('branch') === '__unassigned__') {
                $query->whereDoesntHave('studyRegistrations', fn ($q) => $q->whereNotNull('branch')->where('branch', '!=', ''))
                    ->where(fn ($q) => $q->whereNull('preferences->branch')->orWhere('preferences->branch', ''));
            } else {
                $query->where(fn ($q) => $q->whereHas('studyRegistrations', fn ($registrations) => $registrations->where('branch', $request->input('branch')))
                    ->orWhere('preferences->branch', $request->input('branch')));
            }
        }
        ListCriteria::applyPeriod($query, 'created_at', $request->input('period'));

        $leads = $query->latest()->paginate(20)->withQueryString();
        $counts = $this->visibleLeads()->select('status', DB::raw('COUNT(*) as total'))->groupBy('status')->pluck('total', 'status');
        $sources = $this->visibleLeads()->select('source')->distinct()->orderBy('source')->pluck('source');
        return view('admin.crm.leads.index', ['leads' => $leads, 'counts' => $counts, 'sources' => $sources, 'owners' => $this->owners(), 'routePrefix' => $this->routePrefix()]);
    }

    public function create()
    {
        return view('admin.crm.leads.create', ['owners' => $this->owners(), 'routePrefix' => $this->routePrefix()]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:120'], 'last_name' => ['nullable', 'string', 'max:120'],
            'email' => ['nullable', 'email', 'max:255'], 'phone' => ['nullable', 'string', 'max:60'],
            'source' => ['required', 'string', 'max:80'], 'source_detail' => ['nullable', 'string', 'max:255'],
            'assigned_to' => ['nullable', 'integer', Rule::exists('users', 'id')->whereIn('role', [USER_ROLE_STAFF, USER_ROLE_CONSULTANT])->where('status', STATUS_ACTIVE)],
            'tags' => ['nullable', 'string', 'max:500'], 'note' => ['nullable', 'string', 'max:10000'],
        ]);
        if (empty($data['email']) && empty($data['phone'])) return back()->withInput()->withErrors(['email' => __('Provide at least an email address or phone number.')]);

        $data['email'] = !empty($data['email']) ? strtolower(trim($data['email'])) : null;
        $data['phone'] = !empty($data['phone']) ? preg_replace('/[^0-9+]/', '', trim($data['phone'])) : null;
        $data['tags'] = !empty($data['tags']) ? array_values(array_unique(array_filter(array_map('trim', explode(',', $data['tags']))))) : [];
        $data['created_by'] = auth()->id();
        if ((int) auth()->user()->role === USER_ROLE_CONSULTANT) $data['assigned_to'] = auth()->id();
        DB::transaction(function () use ($data) {
            $note = $data['note'] ?? null;
            unset($data['note']);
            $lead = CrmLead::create($data);
            CrmLeadActivity::create(['lead_id' => $lead->id, 'actor_id' => auth()->id(), 'type' => 'lead_created', 'description' => __('Lead manually created')]);
            if ($note) CrmLeadNote::create(['lead_id' => $lead->id, 'author_id' => auth()->id(), 'body' => $note]);
        });

        return redirect()->route($this->routePrefix() . '.index')->with('success', __('Lead created successfully.'));
    }

    public function show(CrmLead $lead)
    {
        abort_unless($this->visibleLeads()->whereKey($lead->id)->exists(), 404);
        $lead->load(['owner', 'activities.actor', 'notes.author', 'followUps.owner', 'followUps.comments', 'studyRegistrations']);
        return view('admin.crm.leads.show', ['lead' => $lead, 'owners' => $this->owners(), 'routePrefix' => $this->routePrefix()]);
    }

    public function registrationSignature(CrmLead $lead, StudyAbroadRegistration $registration)
    {
        abort_unless($this->visibleLeads()->whereKey($lead->id)->exists(), 404);
        abort_unless((int) $registration->lead_id === (int) $lead->id, 404);
        abort_unless(filled($registration->signature_path), 404);
        abort_unless(Storage::disk('local')->exists($registration->signature_path), 404);

        return Storage::disk('local')->download($registration->signature_path, 'applicant-signature.png');
    }

    public function importForm()
    {
        return view('admin.crm.leads.import', ['routePrefix' => $this->routePrefix()]);
    }

    public function previewImport(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:csv,txt', 'max:2048']]);
        $handle = fopen($request->file('file')->getRealPath(), 'r');
        if (!$handle) return back()->withErrors(['file' => __('Unable to read the uploaded CSV file.')]);

        $headers = fgetcsv($handle);
        if (!$headers || count($headers) > 20) {
            fclose($handle);
            return back()->withErrors(['file' => __('The CSV must include a header row with first_name and an email or phone column.')]);
        }
        $headers = array_map(fn ($header) => Str::of((string) $header)->lower()->trim()->replace([' ', '-'], '_')->toString(), $headers);
        if (count(array_unique($headers)) !== count($headers)) {
            fclose($handle);
            return back()->withErrors(['file' => __('The CSV header contains duplicate column names.')]);
        }
        if (!in_array('first_name', $headers, true) || (!in_array('email', $headers, true) && !in_array('phone', $headers, true))) {
            fclose($handle);
            return back()->withErrors(['file' => __('Required CSV columns: first_name and email or phone. Optional: last_name, email, phone.')]);
        }

        $rows = [];
        $seen = [];
        $rowNumber = 1;
        while (($cells = fgetcsv($handle)) !== false) {
            $rowNumber++;
            if (count($rows) >= 500) break;
            if (count(array_filter($cells, fn ($value) => trim((string) $value) !== '')) === 0) continue;
            $cells = array_pad(array_slice($cells, 0, count($headers)), count($headers), '');
            $data = array_combine($headers, $cells);
            $email = strtolower(trim((string) ($data['email'] ?? '')));
            $phone = preg_replace('/[^0-9+]/', '', trim((string) ($data['phone'] ?? '')));
            $errors = [];
            if (trim((string) ($data['first_name'] ?? '')) === '') $errors[] = __('First name is required');
            if (mb_strlen(trim((string) ($data['first_name'] ?? ''))) > 120 || mb_strlen(trim((string) ($data['last_name'] ?? ''))) > 120) $errors[] = __('Names must be 120 characters or fewer');
            if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = __('Email is invalid');
            if (mb_strlen($email) > 255 || mb_strlen($phone) > 60) $errors[] = __('Email or phone exceeds the maximum field length');
            if (!$email && !$phone) $errors[] = __('Email or phone is required');
            $key = $email ? 'e:' . $email : ($phone ? 'p:' . $phone : 'row:' . $rowNumber);
            $duplicateInFile = isset($seen[$key]);
            $seen[$key] = true;
            $existing = !$errors && !$duplicateInFile && CrmLead::query()->whereNotIn('status', ['converted', 'lost'])->where(function ($query) use ($email, $phone) {
                if ($email) $query->whereRaw('LOWER(email) = ?', [$email]);
                if ($phone) $email ? $query->orWhere('phone', $phone) : $query->where('phone', $phone);
            })->exists();

            $rows[] = [
                'first_name' => trim((string) ($data['first_name'] ?? '')),
                'last_name' => trim((string) ($data['last_name'] ?? '')) ?: null,
                'email' => $email ?: null, 'phone' => $phone ?: null,
                'row' => $rowNumber, 'errors' => $errors,
                'duplicate' => $duplicateInFile || $existing,
            ];
        }
        fclose($handle);
        if (!$rows) return back()->withErrors(['file' => __('No lead rows were found in the CSV.')]);

        $token = (string) Str::uuid();
        session()->put('crm_lead_imports.' . $token, $rows);
        return redirect()->route($this->routePrefix() . '.imports.show', $token);
    }

    public function showImport(string $token)
    {
        $rows = session()->get('crm_lead_imports.' . $token);
        abort_unless(is_array($rows), 404);
        return view('admin.crm.leads.import-preview', ['rows' => $rows, 'token' => $token, 'routePrefix' => $this->routePrefix()]);
    }

    public function commitImport(string $token, Request $request, \App\Services\LeadCaptureService $captureService)
    {
        $rows = session()->get('crm_lead_imports.' . $token);
        abort_unless(is_array($rows), 404);
        $validRows = array_values(array_filter($rows, fn ($row) => empty($row['errors'])));
        if (!$validRows) return back()->withErrors(['file' => __('There are no valid rows to import.')]);

        DB::transaction(function () use ($validRows, $captureService) {
            foreach ($validRows as $row) {
                $captureService->capture($row, 'csv_import', 'CSV row ' . $row['row']);
            }
        });
        session()->forget('crm_lead_imports.' . $token);
        return redirect()->route($this->routePrefix() . '.index')->with('success', __(':count valid rows processed. Existing open leads were merged, not duplicated.', ['count' => count($validRows)]));
    }

    public function bulkAssign(Request $request)
    {
        $data = $request->validate([
            'lead_ids' => ['required', 'array', 'min:1', 'max:100'],
            'lead_ids.*' => ['required', 'integer', 'distinct', Rule::exists('crm_leads', 'id')],
            'assigned_to' => ['nullable', 'integer', Rule::exists('users', 'id')->whereIn('role', [USER_ROLE_STAFF, USER_ROLE_CONSULTANT])->where('status', STATUS_ACTIVE)],
        ]);
        $ownerId = $data['assigned_to'] ?? null;
        if (in_array((int) auth()->user()->role, [USER_ROLE_STAFF, USER_ROLE_CONSULTANT], true) && $ownerId && (int) $ownerId !== (int) auth()->id()) abort(403);

        DB::transaction(function () use ($data, $ownerId) {
            $leads = $this->visibleLeads()->whereIn('id', $data['lead_ids'])->lockForUpdate()->get();
            abort_unless($leads->count() === count($data['lead_ids']), 404);
            foreach ($leads as $lead) {
                if ((int) $lead->assigned_to === (int) $ownerId) continue;
                $oldOwner = $lead->assigned_to;
                $lead->update(['assigned_to' => $ownerId]);
                CrmLeadActivity::create(['lead_id' => $lead->id, 'actor_id' => auth()->id(), 'type' => 'assigned', 'from_value' => (string) $oldOwner, 'to_value' => (string) $ownerId, 'description' => __('Lead assigned in bulk')]);
            }
        });

        return redirect()->route($this->routePrefix() . '.index')->with('success', __('Selected leads assigned successfully.'));
    }

    public function update(Request $request, CrmLead $lead)
    {
        abort_unless($this->visibleLeads()->whereKey($lead->id)->exists(), 404);
        $data = $request->validate([
            'status' => ['required', Rule::in(CrmLead::STATUSES)],
            'assigned_to' => ['nullable', 'integer', Rule::exists('users', 'id')->whereIn('role', [USER_ROLE_STAFF, USER_ROLE_CONSULTANT])->where('status', STATUS_ACTIVE)],
            'lost_reason' => ['nullable', 'string', 'max:255'], 'tags' => ['nullable', 'string', 'max:500'],
        ]);
        if ($data['status'] === 'lost' && empty(trim($data['lost_reason'] ?? ''))) return back()->withInput()->withErrors(['lost_reason' => __('A reason is required when marking a lead lost.')]);
        $allowedTransitions = [
            'new' => ['new', 'contacted', 'lost'],
            'contacted' => ['contacted', 'qualified', 'lost'],
            'qualified' => ['qualified', 'consultation', 'converted', 'lost'],
            'consultation' => ['consultation', 'qualified', 'converted', 'lost'],
            'converted' => ['converted'],
            'lost' => ['lost', 'contacted'],
        ];
        if (!in_array($data['status'], $allowedTransitions[$lead->status] ?? [], true)) {
            return back()->withInput()->withErrors(['status' => __('This stage change is not allowed. Move the lead through the pipeline stages.')]);
        }
        if (in_array((int) auth()->user()->role, [USER_ROLE_STAFF, USER_ROLE_CONSULTANT], true) && isset($data['assigned_to']) && (int) $data['assigned_to'] !== auth()->id()) abort(403);

        DB::transaction(function () use ($lead, $data) {
            $fromStatus = $lead->status;
            $fromOwner = $lead->assigned_to;
            $lead->status = $data['status'];
            $newOwnerId = array_key_exists('assigned_to', $data) ? $data['assigned_to'] : $lead->assigned_to;
            if (in_array((int) auth()->user()->role, [USER_ROLE_STAFF, USER_ROLE_CONSULTANT], true) && (int) $newOwnerId !== (int) auth()->id()) abort(403);
            $lead->assigned_to = $newOwnerId;
            $lead->lost_reason = $data['status'] === 'lost' ? $data['lost_reason'] : null;
            $lead->tags = !empty($data['tags']) ? array_values(array_unique(array_filter(array_map('trim', explode(',', $data['tags']))))) : [];
            $timestamp = now();
            $stampFields = ['contacted' => 'first_contacted_at', 'qualified' => 'qualified_at', 'consultation' => 'consultation_at', 'converted' => 'converted_at', 'lost' => 'lost_at'];
            if (isset($stampFields[$data['status']]) && !$lead->{$stampFields[$data['status']]}) $lead->{$stampFields[$data['status']]} = $timestamp;
            if ($data['status'] !== 'converted') $lead->converted_user_id = null;
            $lead->save();

            if ($fromStatus !== $lead->status) CrmLeadActivity::create(['lead_id' => $lead->id, 'actor_id' => auth()->id(), 'type' => 'stage_changed', 'from_value' => $fromStatus, 'to_value' => $lead->status, 'description' => __('Lead stage changed'), 'metadata' => $data['status'] === 'lost' ? ['lost_reason' => $data['lost_reason']] : null]);
            if ($fromOwner !== $lead->assigned_to) CrmLeadActivity::create(['lead_id' => $lead->id, 'actor_id' => auth()->id(), 'type' => 'assigned', 'from_value' => (string) $fromOwner, 'to_value' => (string) $lead->assigned_to, 'description' => __('Lead assignment updated')]);
        });

        return back()->with('success', __('Lead updated successfully.'));
    }

    public function destroy(CrmLead $lead)
    {
        abort_unless($this->visibleLeads()->whereKey($lead->id)->exists(), 404);
        $lead->delete();

        return redirect()->route($this->routePrefix() . '.index')->with('success', __('Lead moved to deleted records.'));
    }

    public function addNote(Request $request, CrmLead $lead)
    {
        abort_unless($this->visibleLeads()->whereKey($lead->id)->exists(), 404);
        $data = $request->validate(['body' => ['required', 'string', 'max:10000']]);
        DB::transaction(function () use ($lead, $data) {
            CrmLeadNote::create(['lead_id' => $lead->id, 'author_id' => auth()->id(), 'body' => $data['body'], 'is_private' => true]);
            CrmLeadActivity::create(['lead_id' => $lead->id, 'actor_id' => auth()->id(), 'type' => 'note_added', 'description' => __('Internal note added')]);
        });
        return back()->with('success', __('Internal note added.'));
    }

    public function addFollowUp(Request $request, CrmLead $lead)
    {
        abort_unless($this->visibleLeads()->whereKey($lead->id)->exists(), 404);
        $data = $request->validate(['title' => ['required', 'string', 'max:180'], 'description' => ['nullable', 'string', 'max:5000'], 'due_at' => ['required', 'date', 'after_or_equal:now'], 'priority' => ['nullable', Rule::in(['low','normal','high','urgent'])], 'assigned_to' => ['nullable', 'integer', Rule::exists('users', 'id')->whereIn('role', [USER_ROLE_STAFF, USER_ROLE_CONSULTANT])->where('status', STATUS_ACTIVE)]]);
        $data['lead_id'] = $lead->id;
        $data['created_by'] = auth()->id();
        $data['assigned_to'] = $data['assigned_to'] ?? $lead->assigned_to ?? auth()->id();
        if ((int) auth()->user()->role === USER_ROLE_CONSULTANT && (int) $data['assigned_to'] !== (int) auth()->id()) abort(403);
        $data['priority'] = $data['priority'] ?? 'normal';
        $data['status'] = 'open';
        $followUp = CrmFollowUp::create($data);
        CrmLeadActivity::create(['lead_id' => $lead->id, 'actor_id' => auth()->id(), 'type' => 'follow_up_scheduled', 'description' => __('Follow-up scheduled')]);
        app(CrmTaskNotificationOutbox::class)->enqueue('crm-follow-up-created:'.$followUp->id.':'.$followUp->assigned_to, (int) $followUp->assigned_to, __('New lead follow-up assigned'), $followUp->title.' · '.$followUp->due_at->toDateTimeString(), route($this->routePrefix().'.show', $lead));
        app(CrmTaskNotificationOutbox::class)->enqueue('crm-follow-up-due:'.$followUp->id.':'.$followUp->assigned_to, (int) $followUp->assigned_to, __('Lead follow-up due soon'), $followUp->title.' · '.$followUp->due_at->toDateTimeString(), route($this->routePrefix().'.show', $lead), $followUp->due_at->copy()->subDay()->max(now()));
        return back()->with('success', __('Follow-up scheduled.'));
    }

    public function completeFollowUp(CrmFollowUp $followUp)
    {
        abort_unless($this->visibleLeads()->whereKey($followUp->lead_id)->exists(), 404);
        if (in_array((int) auth()->user()->role, [USER_ROLE_STAFF, USER_ROLE_CONSULTANT], true) && (int) $followUp->assigned_to !== (int) auth()->id()) abort(403);
        if (!$followUp->completed_at) {
            $followUp->update(['completed_at' => now(), 'status' => 'completed']);
            \Illuminate\Support\Facades\DB::table('crm_task_notification_outbox')->where('dedupe_key','crm-follow-up-due:'.$followUp->id.':'.$followUp->assigned_to)->whereNull('sent_at')->update(['sent_at'=>now(),'updated_at'=>now()]);
            CrmLeadActivity::create(['lead_id' => $followUp->lead_id, 'actor_id' => auth()->id(), 'type' => 'follow_up_completed', 'description' => __('Follow-up completed')]);
        }
        return back()->with('success', __('Follow-up completed.'));
    }

    public function commentOnFollowUp(Request $request, CrmFollowUp $followUp)
    {
        abort_unless($this->visibleLeads()->whereKey($followUp->lead_id)->exists(), 404);
        if (in_array((int) auth()->user()->role, [USER_ROLE_STAFF, USER_ROLE_CONSULTANT], true) && (int) $followUp->assigned_to !== (int) auth()->id()) abort(403);
        $data = $request->validate(['body' => ['required','string','max:5000']]);
        CrmFollowUpComment::create(['follow_up_id'=>$followUp->id,'author_id'=>auth()->id(),'body'=>$data['body']]);
        CrmLeadActivity::create(['lead_id'=>$followUp->lead_id,'actor_id'=>auth()->id(),'type'=>'follow_up_comment','description'=>__('Internal follow-up comment added')]);
        return back()->with('success', __('Comment added.'));
    }

    private function owners()
    {
        return User::whereIn('role', [USER_ROLE_STAFF, USER_ROLE_CONSULTANT])->where('status', STATUS_ACTIVE)->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'role']);
    }

    private function routePrefix(): string
    {
        return (int) auth()->user()->role === USER_ROLE_CONSULTANT ? 'consultant.crm.leads' : 'admin.crm.leads';
    }
}
