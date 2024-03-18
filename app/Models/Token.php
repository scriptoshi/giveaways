<?php

namespace App\Models;

use App\Enums\TokenType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Token extends Model
{
    use SoftDeletes;
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tokens';

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
        'status' => 'boolean',
        'external' => 'boolean',
        'type' => TokenType::class
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'factory_id',
        'chain_id',
        'name',
        'logo_uri',
        'chainId',
        'symbol',
        'contract_name',
        'decimals',
        'total_supply',
        'contract',
        'type',
        'txhash',
        'status'
    ];


    /**

     * Get the user the token Belongs To.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**

     * Get the chain the token Belongs To.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function chain()
    {
        return $this->belongsTo(Chain::class, 'chain_id', 'id');
    }


    /**
     * Get the badges the token is assigned.
     *
     * @return \Illuminate\Database\Eloquent\Relations\morphMany
     */
    public function badges()
    {
        return $this->morphMany(Badge::class, 'badgeable');
    }


    /**
     * Get the factory the token Belongs To.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function factory()
    {
        return $this->belongsTo(Factory::class, 'factory_id', 'id');
    }

    /**
     * Get the whitelisted address on the token.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function whitelist()
    {
        return $this->morphMany(Whitelist::class, 'whitelistable');
    }
}
