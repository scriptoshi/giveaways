<?php

namespace App\Actions;

use File;
use Storage;

class Upload
{
    public function upload($serverId)
    {
        $filepond = app(\Sopamo\LaravelFilepond\Filepond::class);
        $path = $filepond->getPathFromServerId($serverId);
        $disk = config('filepond.temporary_files_disk');
        $fullpath = Storage::disk($disk)->path($path);
        $uploadedFile = 'uploads/' . File::hash($fullpath) . '.' . File::guessExtension($fullpath);
        File::move($fullpath, Storage::disk('public')->path($uploadedFile));
        return $uploadedFile;
    }
}
