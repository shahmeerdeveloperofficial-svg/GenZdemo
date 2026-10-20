<?php

namespace App\Console\Commands;

use App\Services\CrmTaskNotificationOutbox;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class DeliverCrmTaskNotifications extends Command
{
    protected $signature = 'genz:deliver-crm-task-notifications {--limit=100}';
    protected $description = 'Deliver retryable, deduplicated CRM task notifications.';

    public function handle(CrmTaskNotificationOutbox $outbox): int
    {
        if (!Schema::hasTable('crm_task_notification_outbox')) {
            $this->info('CRM task notification migrations are not installed.');
            return self::SUCCESS;
        }
        $this->info('Delivered '.$outbox->deliverPending((int) $this->option('limit')).' CRM task notification(s).');
        return self::SUCCESS;
    }
}
