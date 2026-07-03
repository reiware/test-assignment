<?php

namespace App\Actions;

use App\Contracts\NotifiesFileDeletion;
use App\Enums\FileDeletionReason;
use App\Jobs\RemovePhysicalFileJob;
use App\Models\FileUpload;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DeleteFileAction
{
    public function __construct(
        private NotifiesFileDeletion $notifier
    ) {}

    public function execute(FileUpload $file, FileDeletionReason $reason): void
    {
        $file->delete();

        RemovePhysicalFileJob::dispatch($file->disk, $file->path);

        try {
            $this->notifier->notify($file, $reason);
        } catch (Throwable $exception) {
            Log::warning('Failed to dispatch file deletion notification.', [
                'file_id' => $file->id,
                'exception' => $exception->getMessage(),
            ]);
        }
    }
}
