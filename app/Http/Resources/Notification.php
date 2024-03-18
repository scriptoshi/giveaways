<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use function PHPUnit\Framework\isNull;

class Notification extends JsonResource
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
            ...$this->data,
            'id' => $this->id,
            'type' => $this->type,
            "read" => isNull($this->read_at),
            "created" => $this->created_at->toAtomString(),
        ];
    }
}
