<?php



namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class User extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'referral' => $this->referral,
            'profile_photo_path' => $this->profile_photo_path,
            'profile_photo_url' => $this->profile_photo_url,
            'use_multiple_accounts' => $this->use_multiple_accounts,
            'slips_count' => $this->slips_count ?? 0,
            'created_at' => $this->created_at->toDateTimeString(),
            'verified_at' => $this->created_at->toDateTimeString(),
            'accounts' => Account::collection($this->whenLoaded('accounts')),
            'account' => new Account($this->whenLoaded('account')),

        ];
    }
}
