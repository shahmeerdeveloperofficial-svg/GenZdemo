<?php

namespace App\Services;

use App\Models\CrmLead;
use App\Models\CrmLeadActivity;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LeadCaptureService
{
    /**
     * Dedupe open enquiries by normalized email first, then phone. Existing leads
     * are enriched without resetting their stage/owner; the repeat intake is audited.
     */
    public function capture(array $data, string $source, ?string $sourceDetail = null, array $preferences = []): CrmLead
    {
        $email = !empty($data['email']) ? Str::lower(trim($data['email'])) : null;
        $phone = !empty($data['phone']) ? preg_replace('/[^0-9+]/', '', trim($data['phone'])) : null;
        if (!$email && !$phone) throw new \InvalidArgumentException('An email address or phone number is required to identify a lead.');
        $connection = DB::connection();
        $lockNames = array_values(array_unique(array_filter([
            $email ? 'genz_lead_' . substr(hash('sha256', 'email:' . $email), 0, 48) : null,
            $phone ? 'genz_lead_' . substr(hash('sha256', 'phone:' . $phone), 0, 48) : null,
        ])));
        sort($lockNames);
        $acquiredLocks = [];
        $transactionSucceeded = false;

        try {
            if ($connection->getDriverName() === 'mysql') {
                foreach ($lockNames as $lockName) {
                    $result = $connection->selectOne('SELECT GET_LOCK(?, 5) AS acquired', [$lockName]);
                    if ((int) ($result->acquired ?? 0) !== 1) throw new \RuntimeException('Lead identity is busy; please retry the enquiry.');
                    $acquiredLocks[] = $lockName;
                }
            }

            $lead = DB::transaction(function () use ($data, $source, $sourceDetail, $preferences, $email, $phone) {
                $leadQuery = CrmLead::query()->whereNotIn('status', ['converted', 'lost']);
                $lead = $email
                    ? (clone $leadQuery)->whereRaw('LOWER(email) = ?', [$email])->lockForUpdate()->first()
                    : null;
                if (!$lead && $phone) $lead = (clone $leadQuery)->where('phone', $phone)->lockForUpdate()->first();
                $isNew = !$lead;
                if (!$lead) {
                    $lead = new CrmLead();
                    $lead->first_name = trim((string) ($data['first_name'] ?? '')) ?: __('Website enquiry');
                    $lead->last_name = trim((string) ($data['last_name'] ?? '')) ?: null;
                    $lead->email = $email;
                    $lead->phone = $phone;
                    $lead->status = 'new';
                    $lead->source = $source;
                    $lead->source_detail = $sourceDetail;
                    $lead->preferences = $preferences ?: null;
                    $lead->save();
                } else {
                    $lead->first_name = $lead->first_name ?: (trim((string) ($data['first_name'] ?? '')) ?: $lead->first_name);
                    $lead->last_name = $lead->last_name ?: (trim((string) ($data['last_name'] ?? '')) ?: null);
                    $lead->email = $lead->email ?: $email;
                    $lead->phone = $lead->phone ?: $phone;
                    if ($preferences) $lead->preferences = array_merge($lead->preferences ?? [], $preferences);
                    $lead->save();
                }

                CrmLeadActivity::create([
                    'lead_id' => $lead->id,
                    'actor_id' => auth()->id(),
                    'type' => $isNew ? 'lead_created' : 'repeat_intake',
                    'description' => $isNew ? __('Lead created from :source', ['source' => $source]) : __('Repeat enquiry received from :source', ['source' => $source]),
                    'metadata' => array_filter(['source_detail' => $sourceDetail]),
                ]);
                return $lead;
            });
            $transactionSucceeded = true;
            return $lead;
        } finally {
            $releaseLocks = function () use ($connection, $acquiredLocks) {
                foreach (array_reverse($acquiredLocks) as $lockName) $connection->selectOne('SELECT RELEASE_LOCK(?)', [$lockName]);
            };
            if ($transactionSucceeded && $connection->transactionLevel() > 0) $connection->afterCommit($releaseLocks);
            else $releaseLocks();
        }
    }
}
