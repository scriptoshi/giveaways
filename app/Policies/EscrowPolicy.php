<?php
namespace App\Policies;

use App\Models\Escrow;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EscrowPolicy
{
    use HandlesAuthorization;
	
	public function before(User $user)
	{
		if ($user->isAdmin()) {
			return true;
		}
	}
	
	 /**
     * Determine whether the user can view the DocEscrow.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Escrow  $escrow
     * @return mixed
     */
    public function viewAny(User $user, Escrow $escrow)
    {
        return $user->hasPermission('viewany.escrow');
    }

    /**
     * Determine whether the user can view the DocEscrow.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Escrow  $escrow
     * @return mixed
     */
    public function view(User $user, Escrow $escrow)
    {
		return $user->hasPermission('view.escrow');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
		return $user->hasPermission('create.escrow');
    }

    /**
     * Determine whether the user can update the DocEscrow.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Escrow  $escrow
     * @return mixed
     */
    public function update(User $user, Escrow $escrow)
    {
        return $user->hasPermission('update.escrow') || $user->id == ($escrow->user_id??null);
    }

    /**
     * Determine whether the user can delete the DocEscrow.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Escrow  $escrow
     * @return mixed
     */
    public function delete(User $user, Escrow $escrow)
    {
        return  $user->hasPermission('delete.escrow')||$user->id == ($escrow->user_id??null);
    }

    /**
     * Determine whether the user can restore the DocEscrow.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Escrow  $escrow
     * @return mixed
     */
    public function restore(User $user, Escrow $escrow)
    {
         return $user->hasPermission('restore.escrow') || $user->id == ($escrow->user_id??null);
    }

    /**
     * Determine whether the user can permanently delete the DocEscrow.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Escrow  $escrow
     * @return mixed
     */
    public function forceDelete(User $user, Escrow $escrow)
    {
        return $user->hasPermission('forcedelete.escrow') || $user->id == ($escrow->user_id??null);		
    }
}