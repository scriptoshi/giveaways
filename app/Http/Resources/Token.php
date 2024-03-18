<?php

namespace App\Http\Resources;

use App\Enums\TokenType;
use Illuminate\Http\Resources\Json\JsonResource;
use Storage;
use Str;

class Token extends JsonResource
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
            'name' => $this->name,
            'logo_uri' => Str::of($this->logo_uri)->startsWith('uploads')
                ? Storage::disk('public')->url($this->logo_uri)
                : $this->logo_uri,
            'chainId' => $this->chainId,
            'chain_id' => $this->chain_id,
            'contract' => $this->contract,
            'txhash' => $this->txhash,
            'symbol' => $this->symbol,
            'contract_name' => $this->contract_name,
            'decimals' => $this->decimals,
            'total_supply' => $this->total_supply * 1,
            'isTax' => $this->type === TokenType::TAXTOKEN,
            'isStandard' => $this->type === TokenType::STANDARD,
            'isCrossChain' => $this->type === TokenType::CROSSCHAIN,
            'type' => $this->type,
            'status' => $this->status,
            'user' => new User($this->whenLoaded('user')),
            'badges' => Badge::collection($this->whenLoaded('badges')),
            'factory' => new Factory($this->whenLoaded('factory')),
            'chain' => new Chain($this->whenLoaded('chain')),
            'whitelist' => Whitelist::collection($this->whenLoaded('whitelist')),
        ];
    }
}
