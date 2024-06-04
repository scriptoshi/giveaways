<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Nft extends JsonResource
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
            'user_id' => $this->user_id,
            'name' => $this->name,
            'symbol' => $this->symbol,
            'contract' => $this->contract,
            'chainId' => $this->chainId,
            'hash' => $this->hash,
            'meta' => $this->meta,
            'giveaway' => new Giveaway($this->whenLoaded('giveaway')),
            'quest' => new Quest($this->whenLoaded('quest')),
            'user' => new User($this->whenLoaded('user')),
            'uploads' => Upload::collection($this->whenLoaded('uploads')),
            'nft' => new Upload($this->whenLoaded('nft')),
        ];
    }
}
