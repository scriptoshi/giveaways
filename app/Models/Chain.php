<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chain extends Model
{
    use SoftDeletes;

    //use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'chains';

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
        'https' => 'array',
        'websockets' => 'array',
        'active' => 'boolean',
        'testnet' => 'boolean'
    ];
    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'type',
        'chainId',
        'websockets',
        'https',
        'explorer',
        'label',
        'active',
        'testnet'
    ];

    /**
     * Get the chain the coin Belongs To.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function coins()
    {
        return $this->hasMany(Coin::class);
    }

    /**
     * Get the chain the coin Belongs To.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function amms()
    {
        return $this->hasMany(Amm::class);
    }

    /**
     * Get the chain the coin Belongs To.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function factories()
    {
        return $this->hasMany(Factory::class);
    }
}
