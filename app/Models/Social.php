<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \App\Enums\SocialNetwork;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Social extends Model
{
    use SoftDeletes;

    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'socials';

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
        'network' => SocialNetwork::class,
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'socialable_type',
        'socialable_id',
        'link',
        'network'
    ];


    /**

     * Get the model that owns this social
     *
     * @return \Illuminate\Database\Eloquent\Relations\morphTo
     */
    public function socialable()
    {
        return $this->morphTo();
    }
}
