<?php



namespace App\Policies;

use App\Models\Chain;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChainPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the DocChain.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chain  $chain
     * @return mixed
     */
    public function viewAny(User $user, Chain $chain)
    {
        //
        return true;
    }

    /**
     * Determine whether the user can view the DocChain.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chain  $chain
     * @return mixed
     */
    public function view(User $user, Chain $chain)
    {
        //
        return true;
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
        return $user->hasPermission('create.chain');
    }

    /**
     * Determine whether the user can update the DocChain.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chain  $chain
     * @return mixed
     */
    public function update(User $user, Chain $chain)
    {
        return $user->hasPermission('update.chain');
    }

    /**
     * Determine whether the user can delete the DocChain.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chain  $chain
     * @return mixed
     */
    public function delete(User $user, Chain $chain)
    {
        //
        return $user->hasPermission('delete.chain');
    }

    /**
     * Determine whether the user can restore the DocChain.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chain  $chain
     * @return mixed
     */
    public function restore(User $user, Chain $chain)
    {
        //
        return $user->hasPermission('restore.chain');
    }

    /**
     * Determine whether the user can permanently delete the DocChain.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Chain  $chain
     * @return mixed
     */
    public function forceDelete(User $user, Chain $chain)
    {
        //
        return $user->hasPermission('forcedelete.chain');
    }
}
