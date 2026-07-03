<?php

namespace App\Notifications;

use App\Contracts\NotifiesFileDeletion;
use App\Enums\FileDeletionReason;
use App\Jobs\NotifyFileDeletionJob;
use App\Models\FileUpload;

class RabbitMqFileDeletionNotifier implements NotifiesFileDeletion
{
    public function notify(FileUpload $file, FileDeletionReason $reason): void
    {
        NotifyFileDeletionJob::dispatch(
            originalName: $file->original_name,
            reason: $reason,
            deletedAt: now()->toDateTimeString(),
        );
    }
}
