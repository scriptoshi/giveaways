<?php

/** dev:
 *Stephen Isaac:  ofuzak@gmail.com.
 *Skype: ofuzak
 */

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Connection extends JsonResource
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
            //'user_id' => $this->user_id,
            //'userId' => $this->userId,
            'provider' => $this->provider,
            'followers' => $this->followers ?? 0,
            'following' => $this->following ?? 0,
            'tweets' => $this->tweets ?? 0,
            'verified' => $this->verified ?? false,
            'description' => $this->description,
            //'token'=> $this->token,
            //'refresh_token'=> $this->refresh_token,
            //'expires_in'=> $this->expires_in,
        ];
    }
}
