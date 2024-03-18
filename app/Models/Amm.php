<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Amm extends Model
{
    use SoftDeletes;
    
        use HasFactory;

	/**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'amms';

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
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'chain_id',
        'name',
        'url',
        'info_url',
        'token_url',
        'pair_url',
        'router',
        'factory',
        'status'
   ];

    
    /**

    * Get the chain the amm Belongs To.
    *
    * @return \Illuminate\Database\Eloquent\Relations\belongsTo
    */
    public function chain()
    {
        return $this->belongsTo(Chain::class, 'chain_id', 'id');
    }
    
}
