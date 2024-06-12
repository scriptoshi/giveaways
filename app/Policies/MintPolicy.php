<?php
namespace App\Policies;

use App\Models\Mint;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MintPolicy
{
    use HandlesAuthorization;
	
	public function before(User $user)
	{
		if ($user->isAdmin()) {
			return true;
		}
	}
	
	 /**
     * Determine whether the user can view the DocMint.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mint  $mint
     * @return mixed
     */
    public function viewAny(User $user, Mint $mint)
    {
        return $user->hasPermission('viewany.mint');
    }

    /**
     * Determine whether the user can view the DocMint.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mint  $mint
     * @return mixed
     */
    public function view(User $user, Mint $mint)
    {
		return $user->hasPermission('view.mint');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
		return $user->hasPermission('create.mint');
    }

    /**
     * Determine whether the user can update the DocMint.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mint  $mint
     * @return mixed
     */
    public function update(User $user, Mint $mint)
    {
        return $user->hasPermission('update.mint') || $user->id == ($mint->user_id??null);
    }

    /**
     * Determine whether the user can delete the DocMint.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mint  $mint
     * @return mixed
     */
    public function delete(User $user, Mint $mint)
    {
        return  $user->hasPermission('delete.mint')||$user->id == ($mint->user_id??null);
    }

    /**
     * Determine whether the user can restore the DocMint.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mint  $mint
     * @return mixed
     */
    public function restore(User $user, Mint $mint)
    {
         return $user->hasPermission('restore.mint') || $user->id == ($mint->user_id??null);
    }

    /**
     * Determine whether the user can permanently delete the DocMint.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Mint  $mint
     * @return mixed
     */
    public function forceDelete(User $user, Mint $mint)
    {
        return $user->hasPermission('forcedelete.mint') || $user->id == ($mint->user_id??null);		
    }
}