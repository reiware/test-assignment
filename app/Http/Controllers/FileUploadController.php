<?php

namespace App\Http\Controllers;

use App\Actions\DeleteFileAction;
use App\Actions\UploadFileAction;
use App\Enums\FileDeletionReason;
use App\Http\Requests\UploadFileRequest;
use App\Models\FileUpload;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileUploadController extends Controller
{
    public function create(): View
    {
        return view('files.create');
    }

    public function index(): View|RedirectResponse
    {
        $files = FileUpload::query()
            ->latest()
            ->paginate(10);

        if ($files->currentPage() > $files->lastPage()) {
            $params = request()->except('page');

            if ($files->lastPage() > 1) {
                $params['page'] = $files->lastPage();
            }

            return redirect()->route('files.index', $params);
        }

        return view('files.index', [
            'files' => $files,
        ]);
    }

    public function store(UploadFileRequest $request, UploadFileAction $upload): JsonResponse
    {
        $files = $upload->execute($request->file('files', []));

        return response()->json([
            'message' => $files->count() > 1
                ? "{$files->count()} files uploaded successfully."
                : 'File uploaded successfully.',
        ]);
    }

    public function download(FileUpload $file): StreamedResponse
    {
        abort_unless(
            Storage::disk($file->disk)->exists($file->path),
            404
        );

        return Storage::disk($file->disk)->download(
            $file->path,
            $file->original_name
        );
    }

    public function destroy(FileUpload $file, DeleteFileAction $delete): JsonResponse
    {
        $delete->execute($file, reason: FileDeletionReason::Manual);

        return response()->json(['message' => 'File deleted successfully.']);
    }
}
