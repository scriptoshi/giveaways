<?php
/** dev:
    *Stephen Isaac:  ofuzak@gmail.com.
    *Skype: ofuzak
 */
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Account extends JsonResource
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
            'id'=>$this->id,
            'user_id'=> $this->user_id,
            'name'=> $this->name,
            'address'=> $this->address,
            'provider'=> $this->provider,
            'created_at'=> $this-> created_at->format('Y-m-d H:i:s'),
            'user'=> new User($this->whenLoaded('user')),
        ];
    }
}
