<?php

namespace App\Contracts;

use App\Models\FileUpload;
use App\Enums\FileDeletionReason;

interface NotifiesFileDeletion
{
    public function notify(FileUpload $file, FileDeletionReason $reason): void;
}
