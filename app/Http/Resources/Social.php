<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Social extends JsonResource
{

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
            'link' => $this->link,
            'url' => route('socials.go', ['to' => $this->code]),
            'name' => ucfirst($this->network->value),
            'network' => $this->network,
            'clicks' => $this->clicks,
            'followers' => $this->clicks,
        ];
    }
}
