<?php
/** dev:
    *Stephen Isaac:  ofuzak@gmail.com.
    *Skype: ofuzak
 */

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;
	
	public function before(User $user)
	{
		if ($user->isAdmin()) {
			return true;
		}
	}
	
	 /**
     * Determine whether the user can view the DocUser.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function viewAny(User $user, User $model)
    {
        return $user->hasPermission('viewany.model');
    }

    /**
     * Determine whether the user can view the DocUser.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function view(User $user, User $model)
    {
		return $user->hasPermission('view.model');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
		return $user->hasPermission('create.model');
    }

    /**
     * Determine whether the user can update the DocUser.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function update(User $user, User $model)
    {
        return $user->hasPermission('update.model') || $user->id == ($model->user_id??null);
    }

    /**
     * Determine whether the user can delete the DocUser.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function delete(User $user, User $model)
    {
        return  $user->hasPermission('delete.model')||$user->id == ($model->user_id??null);
    }

    /**
     * Determine whether the user can restore the DocUser.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function restore(User $user, User $model)
    {
         return $user->hasPermission('restore.model') || $user->id == ($model->user_id??null);
    }

    /**
     * Determine whether the user can permanently delete the DocUser.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\User  $model
     * @return mixed
     */
    public function forceDelete(User $user, User $model)
    {
        return $user->hasPermission('forcedelete.model') || $user->id == ($model->user_id??null);		
    }
}