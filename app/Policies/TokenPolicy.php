<?php

namespace App\Policies;

use App\Models\Token;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TokenPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the DocToken.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Token  $token
     * @return mixed
     */
    public function viewAny(User $user, Token $token)
    {
        return false;
    }

    /**
     * Determine whether the user can view the DocToken.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Token  $token
     * @return mixed
     */
    public function view(User $user, Token $token)
    {
        return $user->id == $token->user_id;
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return true;
    }

    /**
     * Determine whether the user can update the DocToken.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Token  $token
     * @return mixed
     */
    public function update(User $user, Token $token)
    {
        return  $user->id == $token->user_id;
    }

    /**
     * Determine whether the user can delete the DocToken.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Token  $token
     * @return mixed
     */
    public function delete(User $user, Token $token)
    {
        return  $user->id == $token->user_id;
    }

    /**
     * Determine whether the user can restore the DocToken.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Token  $token
     * @return mixed
     */
    public function restore(User $user, Token $token)
    {
        return  false;
    }

    /**
     * Determine whether the user can permanently delete the DocToken.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Token  $token
     * @return mixed
     */
    public function forceDelete(User $user, Token $token)
    {
        return  false;
    }
}
