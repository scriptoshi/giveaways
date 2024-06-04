<?php
namespace App\Policies;

use App\Models\Pump;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PumpPolicy
{
    use HandlesAuthorization;
	
	public function before(User $user)
	{
		if ($user->isAdmin()) {
			return true;
		}
	}
	
	 /**
     * Determine whether the user can view the DocPump.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Pump  $pump
     * @return mixed
     */
    public function viewAny(User $user, Pump $pump)
    {
        return $user->hasPermission('viewany.pump');
    }

    /**
     * Determine whether the user can view the DocPump.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Pump  $pump
     * @return mixed
     */
    public function view(User $user, Pump $pump)
    {
		return $user->hasPermission('view.pump');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
		return $user->hasPermission('create.pump');
    }

    /**
     * Determine whether the user can update the DocPump.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Pump  $pump
     * @return mixed
     */
    public function update(User $user, Pump $pump)
    {
        return $user->hasPermission('update.pump') || $user->id == ($pump->user_id??null);
    }

    /**
     * Determine whether the user can delete the DocPump.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Pump  $pump
     * @return mixed
     */
    public function delete(User $user, Pump $pump)
    {
        return  $user->hasPermission('delete.pump')||$user->id == ($pump->user_id??null);
    }

    /**
     * Determine whether the user can restore the DocPump.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Pump  $pump
     * @return mixed
     */
    public function restore(User $user, Pump $pump)
    {
         return $user->hasPermission('restore.pump') || $user->id == ($pump->user_id??null);
    }

    /**
     * Determine whether the user can permanently delete the DocPump.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Pump  $pump
     * @return mixed
     */
    public function forceDelete(User $user, Pump $pump)
    {
        return $user->hasPermission('forcedelete.pump') || $user->id == ($pump->user_id??null);		
    }
}