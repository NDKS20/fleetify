<?php

namespace App\Helpers;

use Illuminate\Support\Arr;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Upload
{
    public UploadedFile $file;
    public string $path;
    public string $disk;
    public bool $afterCommit;

    // uploaded
    public string $extension;
    public string $key;
    public string $url;
    public string $name;

    public function __construct($file, string $path, bool $afterCommit = true, $disk = 'public')
    {
        $this->file = $file;
        $this->path = $path;
        $this->afterCommit = $afterCommit;
        $this->disk = $disk;

        $this->prepareUpload();

        if ($afterCommit && DB::transactionLevel() > 0) {
            DB::afterCommit(function () {
                $this->commit();
            });

            return;
        }

        $this->commit();
    }

    public function prepareUpload()
    {
        $key = Str::uuid();

        $name = $this->file->getClientOriginalName();
        $extension = $this->file->getClientOriginalExtension();

        $this->extension = $extension;
        $this->key = $key;
        $this->name = "{$this->key}-{$name}";
        $this->url = $this->path . '/' . $this->name;

        return $this;
    }

    public function commit()
    {
        if ($this->file instanceof UploadedFile) {
            $this->file->storeAs($this->path, $this->name, [
                'disk' => $this->disk,
            ]);

            return $this;
        }

        Storage::disk($this->disk)->putFileAs($this->path, $this->file, $this->name);

        return $this;
    }
}

class FileUpload
{
    public static function upload($file, string $path, bool $afterCommit = true, string $disk = 'public', string $driver = 'local')
    {
        return new Upload(
            file: $file,
            path: $path,
            afterCommit: $afterCommit,
            disk: $disk,
        );
    }

    public static function uploadMany(array $files, string $path, bool $afterCommit = true, string $disk = 'public', string $driver = 'local')
    {
        return Arr::map($files, function ($file) use ($path, $afterCommit, $disk, $driver) {
            return new Upload(
                file: $file,
                path: $path,
                afterCommit: $afterCommit,
                disk: $disk,
            );
        });
    }

    public static function delete(string $path, string $disk = 'public')
    {
        Storage::disk($disk)->delete($path);
    }
}
