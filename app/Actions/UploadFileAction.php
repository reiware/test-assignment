<?php

namespace App\Actions;

use App\Models\FileUpload;
use Illuminate\Http\UploadedFile;

class UploadFileAction
{
    public function execute(array $uploadedFiles): \Illuminate\Support\Collection
    {
        return collect($uploadedFiles)->map(function (UploadedFile $uploadedFile) {
            $path = $uploadedFile->store('uploads/files', config('files.disk'));

            if (!$path) {
                throw new \RuntimeException("Failed to store file: {$uploadedFile->getClientOriginalName()}");
            }

            return FileUpload::create([
                'original_name' => $uploadedFile->getClientOriginalName(),
                'path' => $path,
                'disk' => config('files.disk'),
                'mime_type' => $uploadedFile->getMimeType(),
                'extension' => $uploadedFile->extension(),
                'size' => $uploadedFile->getSize(),
                'expires_at' => now()->addHours(config('files.ttl_hours')),
            ]);
        });
    }
}
