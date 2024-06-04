<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Escrow extends JsonResource
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
            'ad_id' => $this->ad_id,
            'user_id' => $this->user_id,
            'coin_id' => $this->coin_id,
            'contract' => $this->contract,
            'amount' => $this->amount,
            'status' => $this->status,
            'user' => new User($this->whenLoaded('user')),
            'ad' => new Ad($this->whenLoaded('ad')),
            'coin' => new Coin($this->whenLoaded('coin')),
        ];
    }
}
