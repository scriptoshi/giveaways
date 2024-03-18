<?php

namespace App\Helpers;

use App\Enums\FactoryType;

class Foundry
{
    public static function factoryType($foundry): FactoryType
    {

        return match ($foundry) {
            'PrivateSaleFoundry' => FactoryType::PRIVATESALEFACTORY,
            'AirdropFoundry' => FactoryType::AIRDROPFACTORY,
            'GovernorFoundry' => FactoryType::GOVERNORFACTORY,
            'MultiSenderFoundry' => FactoryType::MULTISENDERFACTORY,
            'PresaleFoundry' => FactoryType::PRESALEFACTORY,
            'FairLaunchFoundry' => FactoryType::FAIRLAUNCHFACTORY,
            'TaxTokenFoundry' => FactoryType::TAXFACTORY,
            'StakingFoundry' => FactoryType::STAKINGFACTORY,
            'StakingRewardsFoundry' => FactoryType::STAKINGREWARDSFACTORY,
            'TokenFoundry' => FactoryType::STANDARDTOKENFACTORY,
            'LockFoundry' => FactoryType::LOCKFACTORY,
            'DexFoundry' => FactoryType::DEXFACTORY,
            'MigratorFoundry' => FactoryType::MIGRATOR,
            'LzTokenFoundry' => FactoryType::LZFACTORY,
            'NftFoundry' => FactoryType::NFTFACTORY,
            default => null
        };
    }
}
