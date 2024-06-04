<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Ad extends JsonResource
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
            'category' => $this->category,
            'tags' => $this->tags,
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'duration' => $this->duration,
            'period' => $this->period,
            'rating' => $this->rating,
            'telegram' => $this->telegram,
            'url' => $this->url,
            'totalOrders' => $this->totalOrders ?? 0,
            'comments' => $this->comments ?? 0,
            'rating' => $this->rating ?? 0,
            'verified_at' => $this->verified_at,
            'promoted_at' => $this->promoted_at,
            'isPartner' => !!$this->promoted_at,
            'isTopRated' => $this->rating > 4,
            'isOwner' => value(function () use ($request) {
                if (!auth()->check()) return false;
                return $request->user()->id === $this->user_id;
            }),
            'user' => new PublicUser($this->whenLoaded('user')),
            'escrows' => Escrow::collection($this->whenLoaded('escrows')),
            'messages' => Message::collection($this->whenLoaded('messages')),
            'uploads' => Upload::collection($this->whenLoaded('uploads')),
            'image' => new Upload($this->whenLoaded('image')),
        ];
    }
}
