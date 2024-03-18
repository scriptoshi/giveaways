<?php
namespace App\Policies;

use App\Models\Coin;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoinPolicy
{
    use HandlesAuthorization;
	
	public function before(User $user)
	{
		if ($user->isAdmin()) {
			return true;
		}
	}
	
	 /**
     * Determine whether the user can view the DocCoin.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Coin  $coin
     * @return mixed
     */
    public function viewAny(User $user, Coin $coin)
    {
        return $user->hasPermission('viewany.coin');
    }

    /**
     * Determine whether the user can view the DocCoin.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Coin  $coin
     * @return mixed
     */
    public function view(User $user, Coin $coin)
    {
		return $user->hasPermission('view.coin');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
		return $user->hasPermission('create.coin');
    }

    /**
     * Determine whether the user can update the DocCoin.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Coin  $coin
     * @return mixed
     */
    public function update(User $user, Coin $coin)
    {
        return $user->hasPermission('update.coin') || $user->id == ($coin->user_id??null);
    }

    /**
     * Determine whether the user can delete the DocCoin.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Coin  $coin
     * @return mixed
     */
    public function delete(User $user, Coin $coin)
    {
        return  $user->hasPermission('delete.coin')||$user->id == ($coin->user_id??null);
    }

    /**
     * Determine whether the user can restore the DocCoin.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Coin  $coin
     * @return mixed
     */
    public function restore(User $user, Coin $coin)
    {
         return $user->hasPermission('restore.coin') || $user->id == ($coin->user_id??null);
    }

    /**
     * Determine whether the user can permanently delete the DocCoin.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Coin  $coin
     * @return mixed
     */
    public function forceDelete(User $user, Coin $coin)
    {
        return $user->hasPermission('forcedelete.coin') || $user->id == ($coin->user_id??null);		
    }
}