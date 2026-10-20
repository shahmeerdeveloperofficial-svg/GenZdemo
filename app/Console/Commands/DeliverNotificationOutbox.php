<?php

namespace App\Console\Commands;

use App\Services\NotificationDeliveryService;
use Illuminate\Console\Command;

class DeliverNotificationOutbox extends Command
{
    protected $signature='genz:deliver-notifications {--limit=100}';
    protected $description='Deliver deduplicated in-app notifications from the outbox';
    public function handle(NotificationDeliveryService $service): int
    {
        $count=$service->deliverPending((int)$this->option('limit'));
        $this->info("Delivered {$count} in-app notifications.");
        return self::SUCCESS;
    }
}
