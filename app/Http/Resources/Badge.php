<?php

namespace App\Http\Resources;

use App\Traits\WhenMorphed;
use Illuminate\Http\Resources\Json\JsonResource;

class Badge extends JsonResource
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
            'badge' => $this->badge,
            'description' => $this->description,
            'active' => $this->active,
            'inactive' => !$this->active,
            'badgeable' => $this->whenMorphed('badgeable')
        ];
    }
}
