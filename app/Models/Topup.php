<?php

namespace App\Models;

use App\Enums\GiveawayStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Topup extends Model
{
    use SoftDeletes;
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'topups';

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
        'status' => GiveawayStatus::class,
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'giveaway_id',
        'paid',
        'paid_before',
        'prize_before',
        'fee_before',
        'sleep_before',
        'hash',
        'num_winners',
        'num_winners_before',
        'status',
        'account'
    ];


    /**
     * Get the giveaway the topup Belongs To.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function giveaway()
    {
        return $this->belongsTo(Giveaway::class, 'giveaway_id', 'id');
    }
}
