<?php

namespace App\Http\Resources;

use App\Traits\WhenMorphed;
use Illuminate\Http\Resources\Json\JsonResource;

class Message extends JsonResource
{
    use WhenMorphed;
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
            'message_id' => $this->message_id,
            'message' => $this->message,
            'rating' => $this->rating,
            'is_reply' => $this->is_reply,
            'replies' => Message::collection($this->whenLoaded('replies')),
            'msg' => new Message($this->whenLoaded('message')),
            'user' => new User($this->whenLoaded('user')),
            'messageable' =>  $this->whenMorphed('messageable')
        ];
    }
}
