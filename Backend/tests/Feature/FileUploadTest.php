<?php

namespace Tests\Feature;

use App\Helpers\FileUpload;
use App\Helpers\Upload;
use DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Storage;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;

class FileUploadTest extends TestCase
{
    public function test_should_upload_to_public(): void
    {
        Storage::fake();

        $file = UploadedFile::fake()->image('avatar.jpg');
        $output = FileUpload::upload($file, 'avatars', false);

        assertEquals($output->file, $file);
        assertInstanceOf(Upload::class, $output);
        Storage::disk('public')->assertExists($output->url);
    }

    public function test_should_upload_to_local()
    {
        Storage::fake();

        $file = UploadedFile::fake()->image('avatar.jpg');
        $output = FileUpload::upload($file, 'avatars', false, 'local');

        assertEquals($output->file, $file);
        assertInstanceOf(Upload::class, $output);
        Storage::disk('local')->assertExists($output->url);
    }

    public function test_should_only_upload_after_db_commit()
    {
        Storage::fake();

        DB::beginTransaction();

        $file = UploadedFile::fake()->image('avatar.jpg');
        $output = FileUpload::upload($file, 'avatars');

        assertEquals($output->file, $file);
        assertInstanceOf(Upload::class, $output);
        Storage::disk('public')->assertMissing($output->url);

        DB::commit();

        Storage::disk('public')->assertExists($output->url);
    }

    public function test_should_not_upload_if_db_rollback()
    {
        Storage::fake();

        DB::beginTransaction();

        $file = UploadedFile::fake()->image('avatar.jpg');
        $output = FileUpload::upload($file, 'avatars');

        assertEquals($output->file, $file);
        assertInstanceOf(Upload::class, $output);
        Storage::disk('public')->assertMissing($output->url);

        DB::rollBack();

        Storage::disk('public')->assertMissing($output->url);
    }

    public function test_should_upload_even_no_transaction()
    {
        Storage::fake();

        $file = UploadedFile::fake()->image('avatar.jpg');
        $output = FileUpload::upload($file, 'avatars');

        assertEquals($output->file, $file);
        assertInstanceOf(Upload::class, $output);
        Storage::disk('public')->assertExists($output->url);
    }

    public function test_multiple_uploads()
    {
        Storage::fake();

        $files = [
            UploadedFile::fake()->image('avatar1.jpg'),
            UploadedFile::fake()->image('avatar2.jpg'),
            UploadedFile::fake()->image('avatar3.jpg'),
        ];

        $outputs = FileUpload::uploadMany($files, 'avatars', false);

        assertEquals(count($outputs), 3);

        foreach ($outputs as $i => $output) {
            assertInstanceOf(Upload::class, $output);

            assertEquals($output->file, $files[$i]);

            Storage::disk('public')->assertExists($output->url);
        }
    }
}
