<?php

namespace App\Policies;

use App\Models\Quest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestPolicy
{
    use HandlesAuthorization;

    public function before(User $user)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view the DocQuest.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Quest  $quest
     * @return mixed
     */
    public function viewAny(User $user, Quest $quest)
    {
        return true;
    }

    /**
     * Determine whether the user can view the DocQuest.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Quest  $quest
     * @return mixed
     */
    public function view(User $user, Quest $quest)
    {
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
        return true;
    }

    /**
     * Determine whether the user can update the DocQuest.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Quest  $quest
     * @return mixed
     */
    public function update(User $user, Quest $quest)
    {
        return $user->id == $quest->user_id;
    }

    /**
     * Determine whether the user can delete the DocQuest.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Quest  $quest
     * @return mixed
     */
    public function delete(User $user, Quest $quest)
    {
        return $user->id == $quest->user_id;
    }

    /**
     * Determine whether the user can restore the DocQuest.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Quest  $quest
     * @return mixed
     */
    public function restore(User $user, Quest $quest)
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the DocQuest.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Quest  $quest
     * @return mixed
     */
    public function forceDelete(User $user, Quest $quest)
    {
        return false;
    }
}
