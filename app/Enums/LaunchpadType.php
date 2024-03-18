<?php

namespace App\Enums;

enum LaunchpadType: string
{
    case LAUNCHPAD = 'launchpad';
    case PRIVATESALE = 'privatesale';
    case FAIRLAUNCH = 'fairlaunch';
    case FAIRLIQUIDTY = 'fairliquidity';

    /**
     * Get the saleType of the contract.
     * This value corresponds to return of function saleType in the deployed contract.
     * @return int;
     */
    function saleType(): int
    {
        return match ($this) {
            LaunchpadType::LAUNCHPAD => 1,
            LaunchpadType::FAIRLAUNCH, LaunchpadType::FAIRLIQUIDTY  =>  2,
            LaunchpadType::PRIVATESALE => 3,
        };
    }

    /**
     * Get the Human type for the launchpad
     * @return string
     */
    function getType(): string
    {
        return match ($this) {
            LaunchpadType::LAUNCHPAD => 'Presale',
            LaunchpadType::FAIRLAUNCH, LaunchpadType::FAIRLIQUIDTY  =>  'Fair Launch',
            LaunchpadType::PRIVATESALE => 'Private Sale'
        };
    }

    /**
     * Get the factory type that created this launchpad
     * @return \App\Enums\FactoryType;
     */

    function getFactory(): FactoryType
    {
        return match ($this) {
            LaunchpadType::LAUNCHPAD => FactoryType::PRESALEFACTORY,
            LaunchpadType::FAIRLAUNCH, LaunchpadType::FAIRLIQUIDTY  =>  FactoryType::FAIRLAUNCHFACTORY,
            LaunchpadType::PRIVATESALE => FactoryType::PRIVATESALEFACTORY,
            default => FactoryType::PRESALEFACTORY
        };
    }

    /**
     * Determines if the current launchpad is fairlaunch
     * @return bool;
     */
    function isFairLaunch(): bool
    {
        return $this == LaunchpadType::FAIRLAUNCH;
    }
    /**
     * Determines if the current launchpad is privatesale
     * @return bool;
     */
    function isPrivatesale(): bool
    {
        return $this == LaunchpadType::PRIVATESALE;
    }

    /**
     * Determines if the current launchpad is liquidtyLaunch
     * @return bool;
     */
    function isFairLiquidity()
    {
        return $this == LaunchpadType::FAIRLIQUIDTY;
    }

    /**
     * Determines if the current launchpad is presale
     * @return bool;
     */
    function isPresale()
    {
        return $this == LaunchpadType::LAUNCHPAD;
    }
}
