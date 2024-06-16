<?php

namespace App\Http\Resources;

use App\Enums\QuestStatus;
use Illuminate\Http\Resources\Json\JsonResource;

class Quest extends JsonResource
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
            'user_id' => $this->user_id,
            'connection_id' => $this->connection_id,
            'username' => $this->username,
            'name' => str(str($this->username)->explode('/')->last())->explode('?')->first(),
            'complete' => $this->complete ?? false,
            'groupId' => $this->groupId,
            'type' => $this->type,
            'status' => $this->status,
            'live' => $this->status == QuestStatus::ACTIVE,
            'min' => $this->min * 1,
            'gas' => $this->sleep,
            'data' => $this->data,
            'giveaway' => new Giveaway($this->whenLoaded('giveaway')),
            'user' => new User($this->whenLoaded('user')),
            'tasks' => Task::collection($this->whenLoaded('tasks')),
        ];
    }
}
