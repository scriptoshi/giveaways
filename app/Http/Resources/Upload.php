<?php

namespace App\Http\Resources;

use Envatic\CrudStrap\Traits\WhenMorphed;
use Illuminate\Http\Resources\Json\JsonResource;

class Upload extends JsonResource
{
    use WhenMorphed;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'uploadable' => $this->uploadable,
            'key' => $this->key,
            'url' => $this->url,
            'path' => $this->path,
            'drive' => $this->drive,
            'user' => new User($this->whenLoaded('user')),
            'uploadable' => $this->whenMorphed('uploadable')
        ];
    }
}
