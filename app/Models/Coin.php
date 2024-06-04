<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Coin extends Model
{
    use SoftDeletes;
    use HasUuid;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'coins';

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
        'is_native' => 'boolean',
        'active' => 'boolean',
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chain_id',
        'uuid',
        'name',
        'logo_uri',
        'symbol',
        'contract',
        'decimals',
        'usd_rate',
        'is_native',
        'active'
    ];


    /**

     * Get the chain the coin Belongs To.
     */
    public function chain(): BelongsTo
    {
        return $this->belongsTo(Chain::class, 'chain_id', 'id');
    }
}
