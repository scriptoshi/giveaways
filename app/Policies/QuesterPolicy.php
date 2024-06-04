<?php
namespace App\Policies;

use App\Models\Quester;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuesterPolicy
{
    use HandlesAuthorization;
	
	public function before(User $user)
	{
		if ($user->isAdmin()) {
			return true;
		}
	}
	
	 /**
     * Determine whether the user can view the DocQuester.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Quester  $quester
     * @return mixed
     */
    public function viewAny(User $user, Quester $quester)
    {
        return $user->hasPermission('viewany.quester');
    }

    /**
     * Determine whether the user can view the DocQuester.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Quester  $quester
     * @return mixed
     */
    public function view(User $user, Quester $quester)
    {
		return $user->hasPermission('view.quester');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
		return $user->hasPermission('create.quester');
    }

    /**
     * Determine whether the user can update the DocQuester.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Quester  $quester
     * @return mixed
     */
    public function update(User $user, Quester $quester)
    {
        return $user->hasPermission('update.quester') || $user->id == ($quester->user_id??null);
    }

    /**
     * Determine whether the user can delete the DocQuester.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Quester  $quester
     * @return mixed
     */
    public function delete(User $user, Quester $quester)
    {
        return  $user->hasPermission('delete.quester')||$user->id == ($quester->user_id??null);
    }

    /**
     * Determine whether the user can restore the DocQuester.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Quester  $quester
     * @return mixed
     */
    public function restore(User $user, Quester $quester)
    {
         return $user->hasPermission('restore.quester') || $user->id == ($quester->user_id??null);
    }

    /**
     * Determine whether the user can permanently delete the DocQuester.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Quester  $quester
     * @return mixed
     */
    public function forceDelete(User $user, Quester $quester)
    {
        return $user->hasPermission('forcedelete.quester') || $user->id == ($quester->user_id??null);		
    }
}