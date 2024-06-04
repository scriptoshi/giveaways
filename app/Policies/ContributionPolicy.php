<?php
namespace App\Policies;

use App\Models\Contribution;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContributionPolicy
{
    use HandlesAuthorization;
	
	public function before(User $user)
	{
		if ($user->isAdmin()) {
			return true;
		}
	}
	
	 /**
     * Determine whether the user can view the DocContribution.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Contribution  $contribution
     * @return mixed
     */
    public function viewAny(User $user, Contribution $contribution)
    {
        return $user->hasPermission('viewany.contribution');
    }

    /**
     * Determine whether the user can view the DocContribution.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Contribution  $contribution
     * @return mixed
     */
    public function view(User $user, Contribution $contribution)
    {
		return $user->hasPermission('view.contribution');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
		return $user->hasPermission('create.contribution');
    }

    /**
     * Determine whether the user can update the DocContribution.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Contribution  $contribution
     * @return mixed
     */
    public function update(User $user, Contribution $contribution)
    {
        return $user->hasPermission('update.contribution') || $user->id == ($contribution->user_id??null);
    }

    /**
     * Determine whether the user can delete the DocContribution.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Contribution  $contribution
     * @return mixed
     */
    public function delete(User $user, Contribution $contribution)
    {
        return  $user->hasPermission('delete.contribution')||$user->id == ($contribution->user_id??null);
    }

    /**
     * Determine whether the user can restore the DocContribution.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Contribution  $contribution
     * @return mixed
     */
    public function restore(User $user, Contribution $contribution)
    {
         return $user->hasPermission('restore.contribution') || $user->id == ($contribution->user_id??null);
    }

    /**
     * Determine whether the user can permanently delete the DocContribution.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Contribution  $contribution
     * @return mixed
     */
    public function forceDelete(User $user, Contribution $contribution)
    {
        return $user->hasPermission('forcedelete.contribution') || $user->id == ($contribution->user_id??null);		
    }
}