<?php

namespace App\Jobs;

use App\Enums\FileDeletionReason;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\FileDeletedMail;

class NotifyFileDeletionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly string $originalName,
        public readonly FileDeletionReason $reason,
        public readonly string $deletedAt,
    ) {}

    public function handle(): void
    {
        Mail::to(config('files.deletion_email'))
            ->send(new FileDeletedMail(
                originalName: $this->originalName,
                reason: $this->reason,
                deletedAt: $this->deletedAt,
            ));
    }

    public function viaConnection(): string
    {
        return 'rabbitmq';
    }
}
