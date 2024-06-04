<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \App\Enums\EscrowStatus;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Escrow extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'escrows';

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
        'status' => EscrowStatus::class,
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ad_id',
        'user_id',
        'coin_id',
        'contract',
        'amount',
        'status'
    ];


    /**

     * Get the user the escrow Belongs To.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**

     * Get the ad the escrow Belongs To.
     */
    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class, 'ad_id', 'id');
    }

    /**

     * Get the coin the escrow Belongs To.
     */
    public function coin(): BelongsTo
    {
        return $this->belongsTo(Coin::class, 'coin_id', 'id');
    }
}
