<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Project extends JsonResource
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
            'uuid' => $this->uuid,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'rank' => $this->rank,
            'verified_at' => $this->verified_at,
            'promoted_at' => $this->promoted_at,
            'followers' => $this->followers,
            'totalPrize' => ($this->totalPrize ?? 0) * 2,
            'gas' => $this->sleep ?? 0,
            'sale' => $this->sale ?? false,
            'totalGiveaways' => $this->totalGiveaways ?? 0,
            'activeGiveaways' => $this->activeGiveaways ?? 0,
            'isOwner' => value(function () use ($request) {
                if (!auth()->check()) return false;
                return $request->user()->id === $this->user_id;
            }),
            'user' => new User($this->whenLoaded('user')),
            'giveaways' => Giveaway::collection($this->whenLoaded('giveaways')),
            'questers' => Quester::collection($this->whenLoaded('questers')),
            //'nfts' => Nft::collection($this->whenLoaded('nfts')),
            'launchpad' => new Launchpad($this->whenLoaded('launchpad')),
            'uploads' => Upload::collection($this->whenLoaded('uploads')),
            'socials' => Social::collection($this->whenLoaded('socials')),
            'logo' => new Upload($this->whenLoaded('logo')),
        ];
    }
}
