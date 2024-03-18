<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Storage;
use Str;

class Coin extends JsonResource
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
            'chain_id' => $this->chain_id,
            'chainId' => $this->chainId,
            'uuid' => $this->uuid,
            'name' => $this->name,
            'logo_uri' => Str::of($this->logo_uri)->startsWith('http')
                ? $this->logo_uri
                : Storage::disk('public')->url($this->logo_uri),
            'symbol' => $this->symbol,
            'contract' => $this->contract,
            'decimals' => $this->decimals,
            'usd_rate' => in_array($this->symbol, ['USDT,USDC,DAI,FRAX']) ? 1 : (float) $this->usd_rate,
            'is_native' => $this->is_native,
            'active' => $this->active,
            'inactive' => !$this->active,
            'chain' => new Chain($this->whenLoaded('chain')),
        ];
    }
}
