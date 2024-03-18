<?php

namespace App\Helpers;

use App\Enums\FactoryType;

class Slug
{
    public static function factoryType($foundry): FactoryType
    {

        return match ($foundry) {
            'privatesale-factory' => FactoryType::PRIVATESALEFACTORY,
            'airdrop-factory' => FactoryType::AIRDROPFACTORY,
            'badge' => FactoryType::BADGESFACTORY,
            'governor-factory' => FactoryType::GOVERNORFACTORY,
            'multisender' => FactoryType::MULTISENDERFACTORY,
            'presale-factory' => FactoryType::PRESALEFACTORY,
            'fairlaunch-factory' => FactoryType::FAIRLAUNCHFACTORY,
            'tax-token-factory' => FactoryType::TAXFACTORY,
            'staking-factory' => FactoryType::STAKINGFACTORY,
            'stakingrewards-factory' => FactoryType::STAKINGREWARDSFACTORY,
            'token-factory' => FactoryType::STANDARDTOKENFACTORY,
            'token-lock' => FactoryType::LOCKFACTORY,
            'dex-factory' => FactoryType::DEXFACTORY,
            'migrator' => FactoryType::MIGRATOR,
            'nft-factory' => FactoryType::NFTFACTORY,
            'lzfactory-factory' => FactoryType::LZFACTORY,
            default => null
        };
    }
}
