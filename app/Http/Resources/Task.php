<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Task extends JsonResource
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
            'giveaway_id' => $this->giveaway_id,
            'quest_id' => $this->quest_id,
            'quester_id' => $this->quester_id,
            'user_id' => $this->user_id,
            'uuid' => $this->uuid,
            'type' => $this->type,
            'status' => $this->status,
            'response' => $this->response,
            'sleep' => $this->sleep,
            'validated' => $this->validated,
            'giveaway' => new Giveaway($this->whenLoaded('giveaway')),
            'quest' => new Quest($this->whenLoaded('quest')),
            'quester' => new Quester($this->whenLoaded('quester')),
            'user' => new User($this->whenLoaded('user')),
        ];
    }
}
