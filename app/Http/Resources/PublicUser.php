<?php



namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PublicUser extends JsonResource
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
            'name' => $this->name,
            'profile_photo_path' => $this->profile_photo_path,
            'profile_photo_url' => $this->profile_photo_url,
            'account' => new Account($this->whenLoaded('account')),
        ];
    }
}
