<?php

namespace App\Actions;

use File;
use Illuminate\Http\Request;
use Storage;
use Illuminate\Database\Eloquent\Model;

class Uploads
{
    // Save file to upload morphTo table

    private static function filepond($serverId)
    {
        $filepond = app(\Sopamo\LaravelFilepond\Filepond::class);
        $path = $filepond->getPathFromServerId($serverId);
        $disk = config('filepond.temporary_files_disk');
        $fullpath = Storage::disk($disk)->path($path);
        $uploadedFile = 'uploads/' . File::hash($fullpath) . '.' . File::guessExtension($fullpath);
        File::move($fullpath, Storage::disk('public')->path($uploadedFile));
        return $uploadedFile;
    }

    public function upload(Request $request, Model $uploadable, $key = null)
    {
        $uri_key = $key ? "{$key}_uri" : 'uri';
        $upload_key = $key ? "{$key}_upload" : 'upload';
        $path_key = $key ? "{$key}_path" : 'path';
        // user provided a file
        if ($request->input($upload_key) == false) return static::url($request, $uploadable, $key);
        // user uploaded a file
        if (config('app.uploads_disk') === 'public') {
            $path = static::filepond($request->input($uri_key));
            $url = Storage::disk('public')->url($path);
        } else {
            $url = $request->input($uri_key);
            $path = $request->input($path_key);
        }
        $uploadable->uploads()->updateOrCreate([
            'key' => $key,
            'user_id' => $request->user()->id
        ], [
            'url' => $url,
            'path' => $path,
            'drive' => config('app.uploads_disk')
        ]);
    }

    public function validation($uploadkey = null): array
    {
        $key = $uploadkey ? "{$uploadkey}_" : "";
        return [
            "{$key}uri" => ['required', 'string'],
            "{$key}upload" => ['required', 'boolean'],
            "{$key}path" => ['nullable', 'string', "required_if:{$key}upload,true"],
        ];
    }

    /**
     * user provided a file url;
     */
    private static function url(Request $request, Model $uploadable, $key = null)
    {
        $uri_key = $key ? "{$key}_uri" : 'uri';
        $path_key = $key ? "{$key}_path" : 'path';
        $url = $request->input($uri_key);
        $hasPath = $request->filled($path_key);
        $uploadable->uploads()->updateOrCreate([
            'key' => $key,
            'user_id' => $request->user()->id
        ], [
            'url' => $url,
            'is_external' =>  !$hasPath
        ]);
    }
}
