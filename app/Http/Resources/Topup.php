<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Topup extends JsonResource
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
            'paid' => $this->paid,
            'paid_before' => $this->paid_before,
            'prize_before' => $this->prize_before,
            'fee_before' => $this->fee_before,
            'sleep_before' => $this->sleep_before,
            'hash' => $this->hash,
            'num_winners' => $this->num_winners,
            'num_winners_before' => $this->num_winners_before,
            'account' => $this->account,
            'status' => $this->status,
            'giveaway' => new Giveaway($this->whenLoaded('giveaway')),
        ];
    }
}
