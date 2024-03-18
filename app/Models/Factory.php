<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \App\Enums\FactoryType;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Factory extends Model
{
    use SoftDeletes;
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'factories';

    /**
     * The database primary key value.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Attributes that should be cast to native types.
     *
     * @var string
     */
    protected $casts = [
        'type' => FactoryType::class,
        'abi' => 'array',
        'factory_abi' => 'array',
        'active' => 'boolean'
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'foundry',
        'chain_id',
        'version',
        'factory_version',
        'chainId',
        'contract',
        'abi',
        'factory_abi',
        'type',
        'active',
    ];

    /** Scope to select only token factories */
    public function scopeToken(Builder $query)
    {
        $query->where('type', FactoryType::STANDARDTOKENFACTORY);
    }


    /** Scope to select only privatesale factories */
    public function scopePrivatesale(Builder $query)
    {
        $query->where('type', FactoryType::PRIVATESALEFACTORY);
    }

    /** Scope to select only airdrops factory*/
    public function scopeAirdrop(Builder $query)
    {
        $query->where('type', FactoryType::AIRDROPFACTORY);
    }



    /** Scope to select only governor */
    public function scopeGovernor(Builder $query)
    {
        $query->where('type', FactoryType::GOVERNORFACTORY);
    }

    /** Scope to select only multisender */
    public function scopeMultisender(Builder $query)
    {
        $query->where('type', FactoryType::MULTISENDERFACTORY);
    }

    /** Scope to select only presale clone token factories */
    public function scopePresale(Builder $query)
    {
        $query->where('type', FactoryType::PRESALEFACTORY);
    }

    /** Scope to select only fairlaunch clone token factories */
    public function scopeFairlaunch(Builder $query)
    {
        $query->where('type', FactoryType::FAIRLAUNCHFACTORY);
    }

    /** Scope to select only tax clone token factories */
    public function scopeTax(Builder $query)
    {
        $query->where('type', FactoryType::TAXFACTORY);
    }

    /** Scope to select only staking factories */
    public function scopeStaking(Builder $query)
    {
        $query->where('type', FactoryType::STAKINGFACTORY);
    }

    /** Scope to select only stakingrewards factories */
    public function scopeStakingrewards(Builder $query)
    {
        $query->where('type', FactoryType::STAKINGREWARDSFACTORY);
    }

    /** Scope to select only locks factories */
    public function scopeLock(Builder $query)
    {
        $query->where('type', FactoryType::LOCKFACTORY);
    }

    /** Scope to select only dex factories */
    public function scopeDex(Builder $query)
    {
        $query->where('type', FactoryType::DEXFACTORY);
    }


    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function airdrops()
    {
        return $this->hasMany(Airdrop::class);
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function launchpads()
    {
        return $this->hasMany(Launchpad::class);
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function nfts()
    {
        return $this->hasMany(Nft::class);
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function tokens()
    {
        return $this->hasMany(Token::class);
    }


    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function uniswaps()
    {
        return $this->hasMany(Uniswap::class);
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function staking()
    {
        return $this->hasMany(Staking::class);
    }

    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function governors()
    {
        return $this->hasMany(Governor::class);
    }


    /**
     *
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function migrators()
    {
        return $this->hasMany(Migrator::class);
    }
}
