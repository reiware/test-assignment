<?php

namespace Tests\Feature;

use App\Jobs\NotifyFileDeletionJob;
use App\Jobs\RemovePhysicalFileJob;
use App\Models\FileUpload;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('files.disk', 'local');
        config()->set('files.ttl_hours', 24);
        config()->set('files.max_upload_size_kb', 10240);
        config()->set('files.allowed_extensions', ['pdf', 'docx']);

        Storage::fake('local');
    }

    public function test_user_can_view_upload_page(): void
    {
        $response = $this->get(route('files.create'));

        $response->assertOk();
        $response->assertSee('Upload');
    }

    public function test_user_can_view_uploaded_files_page(): void
    {
        FileUpload::create([
            'original_name' => 'test.pdf',
            'path' => 'uploads/files/test.pdf',
            'disk' => 'local',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'size' => 1024,
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->get(route('files.index'));

        $response->assertOk();
        $response->assertSee('test');
        $response->assertSee('PDF');
    }

    public function test_user_can_upload_pdf_file(): void
    {
        $file = UploadedFile::fake()->create(
            'document.pdf',
            100,
            'application/pdf'
        );

        $response = $this->postJson(route('files.store'), [
            'files' => [$file],
        ]);

        $response->assertOk()
            ->assertJsonFragment([
                'message' => 'File uploaded successfully.',
            ]);

        $this->assertDatabaseHas('file_uploads', [
            'original_name' => 'document.pdf',
            'disk' => 'local',
            'extension' => 'pdf',
            'mime_type' => 'application/pdf',
        ]);

        $uploadedFile = FileUpload::first();

        Storage::disk('local')->assertExists($uploadedFile->path);
    }

    public function test_user_can_upload_multiple_files(): void
    {
        $files = [
            UploadedFile::fake()->create('first.pdf', 100, 'application/pdf'),
            UploadedFile::fake()->create('second.pdf', 100, 'application/pdf'),
        ];

        $response = $this->postJson(route('files.store'), [
            'files' => $files,
        ]);

        $response->assertOk()
            ->assertJsonFragment([
                'message' => '2 files uploaded successfully.',
            ]);

        $this->assertDatabaseCount('file_uploads', 2);
    }

    public function test_upload_requires_at_least_one_file(): void
    {
        $response = $this->postJson(route('files.store'), [
            'files' => [],
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['files']);
    }

    public function test_upload_rejects_invalid_file_type(): void
    {
        $file = UploadedFile::fake()->create(
            'notes.txt',
            100,
            'text/plain'
        );

        $response = $this->postJson(route('files.store'), [
            'files' => [$file],
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['files.0']);

        $this->assertDatabaseCount('file_uploads', 0);
    }

    public function test_upload_rejects_file_larger_than_limit(): void
    {
        $file = UploadedFile::fake()->create(
            'large.pdf',
            10241,
            'application/pdf'
        );

        $response = $this->postJson(route('files.store'), [
            'files' => [$file],
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['files.0']);

        $this->assertDatabaseCount('file_uploads', 0);
    }

    public function test_user_can_download_file(): void
    {
        Storage::disk('local')->put('uploads/files/document.pdf', 'test content');

        $file = FileUpload::create([
            'original_name' => 'document.pdf',
            'path' => 'uploads/files/document.pdf',
            'disk' => 'local',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'size' => 12,
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->get(route('files.download', $file));

        $response->assertOk();
        $response->assertHeader('content-disposition');
    }

    public function test_download_returns_404_when_file_does_not_exist_in_storage(): void
    {
        $file = FileUpload::create([
            'original_name' => 'missing.pdf',
            'path' => 'uploads/files/missing.pdf',
            'disk' => 'local',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'size' => 12,
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->get(route('files.download', $file));

        $response->assertNotFound();
    }

    public function test_user_can_delete_file(): void
    {
        Queue::fake();

        Storage::disk('local')->put('uploads/files/document.pdf', 'test content');

        $file = FileUpload::create([
            'original_name' => 'document.pdf',
            'path' => 'uploads/files/document.pdf',
            'disk' => 'local',
            'mime_type' => 'application/pdf',
            'extension' => 'pdf',
            'size' => 12,
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->deleteJson(route('files.destroy', $file));

        $response->assertOk()
            ->assertJsonFragment([
                'message' => 'File deleted successfully.',
            ]);

        $this->assertDatabaseMissing('file_uploads', [
            'id' => $file->id,
        ]);

        Queue::assertPushed(RemovePhysicalFileJob::class, function ($job) use ($file) {
            return $job->disk === $file->disk && $job->path === $file->path;
        });

        Queue::assertPushed(NotifyFileDeletionJob::class);
    }
}
