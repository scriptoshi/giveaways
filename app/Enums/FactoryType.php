<?php

namespace App\Enums;

use File;


enum FactoryType: string
{
    case PRIVATESALEFACTORY = 'PrivateSaleFactory';
    case AIRDROPFACTORY = 'AirdropFactory';
    case GOVERNORFACTORY = 'GovernorFactory';
    case MULTISENDERFACTORY = 'Multisender';
    case PRESALEFACTORY = 'PresaleFactory';
    case FAIRLAUNCHFACTORY = 'FairLaunchFactory';
    case TAXFACTORY = 'TaxFactory';
    case STAKINGFACTORY = 'StakingFactory';
    case STAKINGREWARDSFACTORY = 'StakingRewardsFactory';
    case STANDARDTOKENFACTORY = 'StandardTokenFactory';
    case LOCKFACTORY = 'TokenLock';
    case DEXFACTORY = 'DexFactory';
    case NFTFACTORY = 'NftFactory';
    case LZFACTORY = "LZFactory";
    case MIGRATOR = "Migrator";

    function foundryInfo()
    {
        return match ($this) {
            FactoryType::PRIVATESALEFACTORY => json_decode(File::get(resource_path('/js/foundries/PrivateSaleFoundry.json'))),
            FactoryType::AIRDROPFACTORY => json_decode(File::get(resource_path('/js/foundries/AirdropFoundry.json'))),
            FactoryType::GOVERNORFACTORY => json_decode(File::get(resource_path('/js/foundries/GovernorFoundry.json'))),
            FactoryType::MULTISENDERFACTORY => json_decode(File::get(resource_path('/js/foundries/MultiSenderFoundry.json'))),
            FactoryType::PRESALEFACTORY => json_decode(File::get(resource_path('/js/foundries/PresaleFoundry.json'))),
            FactoryType::FAIRLAUNCHFACTORY => json_decode(File::get(resource_path('/js/foundries/FairLaunchFoundry.json'))),
            FactoryType::TAXFACTORY => json_decode(File::get(resource_path('/js/foundries/TaxTokenFoundry.json'))),
            FactoryType::STAKINGFACTORY => json_decode(File::get(resource_path('/js/foundries/StakingFoundry.json'))),
            FactoryType::STAKINGREWARDSFACTORY => json_decode(File::get(resource_path('/js/foundries/StakingRewardsFoundry.json'))),
            FactoryType::STANDARDTOKENFACTORY => json_decode(File::get(resource_path('/js/foundries/TokenFoundry.json'))),
            FactoryType::LOCKFACTORY => json_decode(File::get(resource_path('/js/foundries/LockFoundry.json'))),
            FactoryType::DEXFACTORY => json_decode(File::get(resource_path('/js/foundries/DexFoundry.json'))),
            FactoryType::MIGRATOR => json_decode(File::get(resource_path('/js/foundries/MigratorFoundry.json'))),
            FactoryType::LZFACTORY => json_decode(File::get(resource_path('/js/foundries/LzTokenFoundry.json'))),
            FactoryType::NFTFACTORY => json_decode(File::get(resource_path('/js/foundries/NftFoundry.json'))),
            default => null
        };
    }

    function isDeployable()
    {
        return match ($this) {
            FactoryType::MULTISENDERFACTORY,
            FactoryType::LOCKFACTORY => false,
            default => true
        };
    }
    function name()
    {
        return match ($this) {
            FactoryType::PRIVATESALEFACTORY => 'Private sale',
            FactoryType::AIRDROPFACTORY => 'Airdrop',
            FactoryType::GOVERNORFACTORY => 'Governance Token',
            FactoryType::MULTISENDERFACTORY => 'Multi Sender',
            FactoryType::PRESALEFACTORY => 'ICO IDO',
            FactoryType::FAIRLAUNCHFACTORY => 'Fair launch',
            FactoryType::TAXFACTORY => 'Tax Token',
            FactoryType::STAKINGFACTORY => 'Staking',
            FactoryType::STAKINGREWARDSFACTORY => 'Lp Farming',
            FactoryType::STANDARDTOKENFACTORY => 'ERC20 Token',
            FactoryType::LOCKFACTORY => 'Lp and Token Lock',
            FactoryType::DEXFACTORY => 'Amm Exchange',
            FactoryType::MIGRATOR => "Token Migrator",
            FactoryType::LZFACTORY => 'Cross chain Token',
            FactoryType::NFTFACTORY => "Nft Launchpad",
            default => null
        };
    }

    function isLaunchpad()
    {
        return match ($this) {
            FactoryType::PRIVATESALEFACTORY, FactoryType::PRESALEFACTORY, FactoryType::FAIRLAUNCHFACTORY  => true,
            default => false
        };
    }
    function isFairLaunch()
    {
        return $this == FactoryType::FAIRLAUNCHFACTORY;
    }
}
