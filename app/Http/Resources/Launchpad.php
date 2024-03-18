<?php

namespace App\Http\Resources;

use App\Enums\LaunchpadType;
use Illuminate\Http\Resources\Json\JsonResource;

class Launchpad extends JsonResource
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
            'coin_id' => $this->coin_id,
            'chainId' => $this->chainId,
            'token_id' => $this->token_id,
            'amm_id' => $this->amm_id,
            'listing_price' => $this->listing_price,
            'presale_price' => $this->presale_price,
            'softcap' => $this->softcap,
            'hardcap' => $this->hardcap,
            'participants' => $this->participants,
            'presale_amount' => $this->presale_amount,
            'contract' => $this->contract,
            'verified' => $this->verified,
            'unsold_tokens' => $this->unsold_tokens,
            'base_token' => $this->base_token,
            'type' => $this->type,
            'starts_at' => $this->starts_at,
            'startTime' => $this->starts_at?->timestamp,
            'ends_at' => $this->ends_at,
            'endTime' => $this->ends_at?->timestamp,
            'is_finalized' => $this->is_finalized,
            'pair' => $this->pair,
            'txhash' => $this->txhash,
            'status' => $this->status,
            'total' => $this->total,
            'lock_period' => $this->lock_period,
            'percent' => $this->percent,
            'liquidity_percent' => $this->liquidity_percent,
            //'status_code' => $this->status_code,
            'base_deposited' => $this->base_deposited ?? 0,
            'total_tokens' => $this->total_tokens ?? 0,
            'version' => $this->version == 'v1' ? 'default' : $this->version,

            //project
            'name' => $this->name,
            'description' => $this->description,
            //token
            'token_name' => $this->token_name,
            'token_contract' => $this->token_contract,
            'token_decimals' => $this->token_decimals,
            'token_supply' => $this->token_supply,
            'token_symbol' => $this->token_symbol,
            'logo_uri' => $this->logo_uri,
            //stats
            'min' => $this->min,
            'max' => $this->max,
            'loves' => $this->loves ?? 0,
            'hates' => $this->hates ?? 0,
            'loved' => $this->loved ?? false,
            'hated' => $this->hated ?? false,
            'liked' => $this->liked ?? false,
            'likes' => $this->likeCount,
            'hits' => $this->hits,
            // derived
            'subscribed' => $this->subscribed ?? false,
            'featured' => $this->featured ?? false,
            'statusCode' => $this->status->statusCode(),
            'isPrivateSale' => $this->type == LaunchpadType::PRIVATESALE,
            'isFairLaunch' => $this->type == LaunchpadType::FAIRLAUNCH,
            'isLpLaunch' => $this->type == LaunchpadType::FAIRLIQUIDTY,
            'isPresale' => $this->type == LaunchpadType::LAUNCHPAD,
            'isDutchAuction' => $this->type == LaunchpadType::DUTCHAUCTION,
            'isConstantProduct' => $this->type == LaunchpadType::CONSTANTPRODUCT,
            'total_base_usd' => $this->whenLoaded('coin', ($this->base_deposited ?? 0 * $this->coin->usd_rate) * 1),
            'link' => route('launchpads.show', ['chainId' => $this->chainId, 'launchpad' => $this->contract]),
            'coin' => new Coin($this->whenLoaded('coin')),
            'whitelist' => Whitelist::collection($this->whenLoaded('whitelist')),
            'user' => new User($this->whenLoaded('user')),
            'amm' => new Amm($this->whenLoaded('amm')),
            'factory' => new Factory($this->whenLoaded('factory')),
            'socials' =>  Social::collection($this->whenLoaded('socials')),
            'accounts' =>  Account::collection($this->whenLoaded('accounts')),
        ];
    }
}
