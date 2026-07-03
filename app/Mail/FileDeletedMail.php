<?php

namespace App\Mail;

use App\Enums\FileDeletionReason;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FileDeletedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $originalName,
        public readonly FileDeletionReason $reason,
        public readonly string $deletedAt,
    ) {}

    public function build(): self
    {
        return $this->subject('File deleted: ' . $this->originalName)
            ->view('emails.file-deleted');
    }
}
