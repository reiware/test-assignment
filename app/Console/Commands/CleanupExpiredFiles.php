<?php

namespace App\Console\Commands;

use App\Actions\DeleteFileAction;
use App\Enums\FileDeletionReason;
use App\Models\FileUpload;
use Illuminate\Console\Command;

class CleanupExpiredFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'files:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired uploaded files';

    /**
     * Execute the console command.
     */
    public function handle(DeleteFileAction $delete): int
    {
        $deletedCount = 0;

        FileUpload::query()
            ->where('expires_at', '<=', now())
            ->chunkById(100, function ($files) use ($delete, &$deletedCount) {
                foreach ($files as $file) {
                    $delete->execute($file, reason: FileDeletionReason::Expired);
                    $deletedCount++;
                }
            });

        $this->info("Deleted {$deletedCount} expired files");

        return self::SUCCESS;
    }
}
