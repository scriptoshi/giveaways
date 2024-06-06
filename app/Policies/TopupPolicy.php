<?php
namespace App\Policies;

use App\Models\Topup;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TopupPolicy
{
    use HandlesAuthorization;
	
	public function before(User $user)
	{
		if ($user->isAdmin()) {
			return true;
		}
	}
	
	 /**
     * Determine whether the user can view the DocTopup.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Topup  $topup
     * @return mixed
     */
    public function viewAny(User $user, Topup $topup)
    {
        return $user->hasPermission('viewany.topup');
    }

    /**
     * Determine whether the user can view the DocTopup.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Topup  $topup
     * @return mixed
     */
    public function view(User $user, Topup $topup)
    {
		return $user->hasPermission('view.topup');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
		return $user->hasPermission('create.topup');
    }

    /**
     * Determine whether the user can update the DocTopup.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Topup  $topup
     * @return mixed
     */
    public function update(User $user, Topup $topup)
    {
        return $user->hasPermission('update.topup') || $user->id == ($topup->user_id??null);
    }

    /**
     * Determine whether the user can delete the DocTopup.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Topup  $topup
     * @return mixed
     */
    public function delete(User $user, Topup $topup)
    {
        return  $user->hasPermission('delete.topup')||$user->id == ($topup->user_id??null);
    }

    /**
     * Determine whether the user can restore the DocTopup.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Topup  $topup
     * @return mixed
     */
    public function restore(User $user, Topup $topup)
    {
         return $user->hasPermission('restore.topup') || $user->id == ($topup->user_id??null);
    }

    /**
     * Determine whether the user can permanently delete the DocTopup.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Topup  $topup
     * @return mixed
     */
    public function forceDelete(User $user, Topup $topup)
    {
        return $user->hasPermission('forcedelete.topup') || $user->id == ($topup->user_id??null);		
    }
}