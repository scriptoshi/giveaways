<?php

namespace App\Policies;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the DocAd.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ad  $ad
     * @return mixed
     */
    public function viewAny(User $user, Ad $ad)
    {
        return  true;
    }

    /**
     * Determine whether the user can view the DocAd.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ad  $ad
     * @return mixed
     */
    public function view(User $user, Ad $ad)
    {
        return  true;
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return  true;
    }

    /**
     * Determine whether the user can update the DocAd.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ad  $ad
     * @return mixed
     */
    public function update(User $user, Ad $ad)
    {
        return $user->id == $ad->user_id;
    }

    /**
     * Determine whether the user can delete the DocAd.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ad  $ad
     * @return mixed
     */
    public function delete(User $user, Ad $ad)
    {
        return $user->id == $ad->user_id;
    }

    /**
     * Determine whether the user can restore the DocAd.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ad  $ad
     * @return mixed
     */
    public function restore(User $user, Ad $ad)
    {
        return  false;
    }

    /**
     * Determine whether the user can permanently delete the DocAd.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Ad  $ad
     * @return mixed
     */
    public function forceDelete(User $user, Ad $ad)
    {
        return false;
    }
}
