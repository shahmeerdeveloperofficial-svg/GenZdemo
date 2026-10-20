<?php

namespace App\Services;

use App\Models\Notification;
use DateTimeInterface;
use Illuminate\Support\Facades\DB;

class CrmTaskNotificationOutbox
{
    public function enqueue(string $key, int $recipientId, string $title, string $body, ?string $link = null, ?DateTimeInterface $availableAt = null): void
    {
        DB::table('crm_task_notification_outbox')->insertOrIgnore([
            'dedupe_key' => $key, 'recipient_id' => $recipientId, 'title' => $title, 'body' => $body, 'link' => $link,
            'available_at' => $availableAt ?? now(), 'created_at' => now(), 'updated_at' => now(),
        ]);
    }

    public function deliverPending(int $limit = 100): int
    {
        $sent = 0;
        $items = DB::table('crm_task_notification_outbox')->whereNull('sent_at')->where('available_at','<=',now())->orderBy('id')->limit(max(1,min($limit,500)))->get();
        foreach ($items as $item) {
            try {
                DB::transaction(function () use ($item) {
                    $locked = DB::table('crm_task_notification_outbox')->where('id',$item->id)->lockForUpdate()->first();
                    if (!$locked || $locked->sent_at || $locked->available_at > now()) return;
                    if (!Notification::where('dedupe_key',$locked->dedupe_key)->exists()) {
                        Notification::create(['user_id'=>$locked->recipient_id,'title'=>$locked->title,'body'=>$locked->body,'link'=>$locked->link,'dedupe_key'=>$locked->dedupe_key]);
                    }
                    DB::table('crm_task_notification_outbox')->where('id',$locked->id)->update(['sent_at'=>now(),'updated_at'=>now()]);
                });
                $sent++;
            } catch (\Throwable $exception) {
                report($exception);
                $attempts = (int) $item->attempts + 1;
                DB::table('crm_task_notification_outbox')->where('id',$item->id)->whereNull('sent_at')->update([
                    'attempts'=>$attempts,'last_error'=>mb_substr($exception->getMessage(),0,2000),
                    'available_at'=>now()->addSeconds(min(3600, 30 * (2 ** min($attempts,7)))),'updated_at'=>now(),
                ]);
            }
        }
        return $sent;
    }
}
