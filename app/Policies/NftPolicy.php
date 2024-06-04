<?php
namespace App\Policies;

use App\Models\Nft;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class NftPolicy
{
    use HandlesAuthorization;
	
	public function before(User $user)
	{
		if ($user->isAdmin()) {
			return true;
		}
	}
	
	 /**
     * Determine whether the user can view the DocNft.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nft  $nft
     * @return mixed
     */
    public function viewAny(User $user, Nft $nft)
    {
        return $user->hasPermission('viewany.nft');
    }

    /**
     * Determine whether the user can view the DocNft.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nft  $nft
     * @return mixed
     */
    public function view(User $user, Nft $nft)
    {
		return $user->hasPermission('view.nft');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
		return $user->hasPermission('create.nft');
    }

    /**
     * Determine whether the user can update the DocNft.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nft  $nft
     * @return mixed
     */
    public function update(User $user, Nft $nft)
    {
        return $user->hasPermission('update.nft') || $user->id == ($nft->user_id??null);
    }

    /**
     * Determine whether the user can delete the DocNft.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nft  $nft
     * @return mixed
     */
    public function delete(User $user, Nft $nft)
    {
        return  $user->hasPermission('delete.nft')||$user->id == ($nft->user_id??null);
    }

    /**
     * Determine whether the user can restore the DocNft.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nft  $nft
     * @return mixed
     */
    public function restore(User $user, Nft $nft)
    {
         return $user->hasPermission('restore.nft') || $user->id == ($nft->user_id??null);
    }

    /**
     * Determine whether the user can permanently delete the DocNft.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Nft  $nft
     * @return mixed
     */
    public function forceDelete(User $user, Nft $nft)
    {
        return $user->hasPermission('forcedelete.nft') || $user->id == ($nft->user_id??null);		
    }
}