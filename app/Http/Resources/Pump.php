<?php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Pump extends JsonResource
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
            'id'=>$this->id,
            'quester_id'=> $this->quester_id,
            'quester'=> new Quester($this->whenLoaded('quester')),
        ];
    }
}
