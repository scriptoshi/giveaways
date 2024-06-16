<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Quester extends JsonResource
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
            'percent' => $this->percent,
            'uuid' => $this->uuid,
            'qid' => $this->qid,
            'pump' => $this->pump,
            'gas' => $this->gas,
            'address' => $this->address,
            'status' => $this->status,
            'comment' => $this->comment,
            'signature' => $this->signature,
            'claim' => $this->claim,
            'completed_at' => $this->completed_at,
            'gas_signature' => $this->gas_signature,
            'gas_hash' => $this->gas_hash,
            'gas_claim' => $this->gas_claim,
            'gas_claimed_at' => $this->gas_claimed_at,
            'isComplete' => !!$this->completed_at,
            'rank' => $this->rank, // sum of pumps on this vote.
            'boosted_at' => $this->boosted_at ? $this->boosted_at?->toDateTimeString() : null,
            'last_pump_at' => $this->last_pump_at ? $this->last_pump_at?->toDateTimeString() : null,
            'last_pump_ago' => $this->last_pump_at ?  $this->last_pump_at?->diffForHumans() : null,
            'pumps_24' => $this->pumps_24,
            'pumps_7' => $this->pumps_7,
            'pumps_30' => $this->pumps_30,
            'isMe' => value(function () use ($request) {
                if (!auth()->check()) return false;
                return $request->user()->id === $this->user_id;
            }),
            'giveaway' => new Giveaway($this->whenLoaded('giveaway')),
            'user' => new PublicUser($this->whenLoaded('user')),
            'pumps' => Pump::collection($this->whenLoaded('pumps')),
        ];
    }
}
