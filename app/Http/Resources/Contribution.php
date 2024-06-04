<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Contribution extends JsonResource
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
            'user_id' => $this->user_id,
            'project_id' => $this->project_id,
            'launchpad_id' => $this->launchpad_id,
            'amount' => $this->amount,
            'txhash' => $this->txhash,
            'user' => new User($this->whenLoaded('user')),
            'project' => new Project($this->whenLoaded('project')),
            'launchpad' => new Launchpad($this->whenLoaded('launchpad')),
        ];
    }
}
