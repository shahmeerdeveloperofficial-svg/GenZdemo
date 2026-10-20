<?php

namespace App\Services;

use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class NotificationDeliveryService
{
    public function enqueue(string $key,int $recipientId,string $event,string $title,string $body,?string $link=null): void
    {
        if (Schema::hasTable('notification_preferences') && DB::table('notification_preferences')->where('user_id',$recipientId)->where('event_key',$event)->where('channel','in_app')->where('enabled',false)->exists()) return;
        DB::table('notification_outbox')->insertOrIgnore(['dedupe_key'=>$key,'recipient_id'=>$recipientId,'event_key'=>$event,'channel'=>'in_app','title'=>$title,'body'=>$body,'link'=>$link,'available_at'=>now(),'created_at'=>now(),'updated_at'=>now()]);
    }

    public function deliverPending(int $limit=100): int
    {
        $delivered=0;
        $rows=DB::table('notification_outbox')->where('channel','in_app')->whereNull('sent_at')->where('available_at','<=',now())->orderBy('id')->limit(max(1,min($limit,500)))->get();
        foreach($rows as $row) {
            try {
                DB::transaction(function() use($row) {
                    $locked=DB::table('notification_outbox')->where('id',$row->id)->lockForUpdate()->first();
                    if(!$locked || $locked->sent_at || $locked->available_at>now()) return;
                    if(!Notification::where('dedupe_key',$locked->dedupe_key)->exists()) Notification::create(['user_id'=>$locked->recipient_id,'title'=>$locked->title,'body'=>$locked->body,'link'=>$locked->link,'dedupe_key'=>$locked->dedupe_key]);
                    DB::table('notification_outbox')->where('id',$locked->id)->update(['sent_at'=>now(),'updated_at'=>now()]);
                });
                $delivered++;
            } catch(\Throwable $exception) {
                report($exception); $attempts=(int)$row->attempts+1;
                DB::table('notification_outbox')->where('id',$row->id)->whereNull('sent_at')->update(['attempts'=>$attempts,'last_error'=>mb_substr($exception->getMessage(),0,2000),'available_at'=>now()->addSeconds(min(3600,30*(2**min($attempts,7)))),'updated_at'=>now()]);
            }
        }
        return $delivered;
    }
}
