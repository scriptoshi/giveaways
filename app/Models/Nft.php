<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nft extends Model
{
    use SoftDeletes;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'nfts';

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


    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'giveaway_id',
        'quest_id',
        'user_id',
        'name',
        'symbol',
        'contract',
        'chainId',
        'hash',
        'meta'
    ];


    /**

     * Get the giveaway the nft Belongs To.
     */
    public function giveaway(): BelongsTo
    {
        return $this->belongsTo(Giveaway::class, 'giveaway_id', 'id');
    }

    /**

     * Get the quest the nft Belongs To.
     */
    public function quest(): BelongsTo
    {
        return $this->belongsTo(Quest::class, 'quest_id', 'id');
    }

    /**

     * Get the user the nft Belongs To.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**

     * Get the uploads the nft Belongs To.
     */
    public function uploads(): MorphMany
    {
        return $this->morphMany(Upload::class, 'uploadable');
    }

    /**

     * Get the nft the nft Belongs To.
     */
    public function nft(): MorphOne
    {
        return $this->morphOne(Upload::class, 'uploadable')->where('key', 'nft');
    }
}
