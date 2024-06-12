<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mint extends Model
{
    use SoftDeletes;

    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'mints';

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
        'verified' => 'boolean',
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'owner',
        'nft_contract',
        'nft',
        'tokenId',
        'chainId',
        'txhash',
        'verified'
    ];


    /**

     * Get the user the mint Belongs To.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
