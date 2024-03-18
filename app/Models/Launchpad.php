<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use \App\Enums\LaunchpadUnsold_tokens;
use \App\Enums\LaunchpadType;
use \App\Enums\LaunchpadStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Qirolab\Laravel\Reactions\Traits\Reactable;
use Qirolab\Laravel\Reactions\Contracts\ReactableInterface;
use CyrildeWit\EloquentViewable\InteractsWithViews;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use Illuminate\Notifications\Notifiable;
use Conner\Likeable\Likeable;
use Ixudra\Curl\Facades\Curl;

class Launchpad extends Model implements ReactableInterface, Viewable
{
    use SoftDeletes;
    use Reactable;
    use HasFactory;
    use InteractsWithViews;
    use Notifiable;
    use Likeable;


    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'launchpads';

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
        'burn' => 'boolean',
        'is_force_failed' => 'boolean',
        'is_cancelled' => 'boolean',
        'starts_at' => 'datetime',
        'is_featured' => 'boolean',
        'is_finalized' => 'boolean',
        'ends_at' => 'datetime',
        'unsold_tokens' => LaunchpadUnsold_tokens::class,
        'type' => LaunchpadType::class,
        'status' => LaunchpadStatus::class,
    ];

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'coin_id',
        'token_id',
        'factory_id',
        'amm_id',
        'participants',
        'listing_price',
        'presale_price',
        'is_force_failed',
        'is_cancelled',
        'total_base',
        'softcap',
        'hardcap',
        'min',
        'max',
        'presale_amount',
        'contract',
        'unsold_tokens',
        'is_featured',
        'base_token',
        'type',
        'starts_at',
        'is_finalized',
        'pair',
        'ends_at',
        'txhash',
        'status',
        'total',
        'lock_period',
        'percentage',
        'liquidity_percent',
        'status_code',
        'base_deposited',
        'total_tokens',
        //project
        'name',
        'description',
        //token
        'token_name',
        'token_contract',
        'token_decimals',
        'token_supply',
        'token_symbol',
        'logo_uri',
    ];


    /**

     * Get the coin the launchpad Belongs To.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function coin()
    {
        return $this->belongsTo(Coin::class, 'coin_id', 'id');
    }

    /**

     * Get the user the launchpad Belongs To.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get the user the launchpad Belongs To.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function factory()
    {
        return $this->belongsTo(Factory::class, 'factory_id', 'id');
    }

    /**
     * Get the amm the launchpad Belongs To.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsTo
     */
    public function amm()
    {
        return $this->belongsTo(Amm::class, 'amm_id', 'id');
    }

    /**
     * Get the badges the launchpads Has.
     *
     * @return \Illuminate\Database\Eloquent\Relations\morphToMany
     */
    public function badges()
    {
        return $this->morphToMany(Badge::class, 'badgeable');
    }

    /**
     * Get the accounts that contributed to the launchpad.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function accounts()
    {
        return $this->morphToMany(Account::class, 'accountable');
    }

    /**
     * Get the whitelist the launchpads has.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function whitelist()
    {
        return $this->morphMany(Whitelist::class, 'whitelistable');
    }

    /**
     * Get the whitelist the launchpads has.
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function socials()
    {
        return $this->morphMany(Social::class, 'socialable');
    }



    public function routeNotificationForPusherPushNotifications($notification): string
    {
        return $this->contract;
    }


    /**
     * Route notifications for the Telegram channel.
     *
     * @return int
     */
    public function routeNotificationForTelegram()
    {
        return  config('app.telegram_channel');
    }

    /**
     * Get the subscibers to notifications
     *
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function subscribers()
    {
        return $this->belongsToMany(User::class, 'launchpad_user', 'launchpad_id', 'user_id');
    }
}
