<?php
namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;
	
	public function before(User $user)
	{
		if ($user->isAdmin()) {
			return true;
		}
	}
	
	 /**
     * Determine whether the user can view the DocTask.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return mixed
     */
    public function viewAny(User $user, Task $task)
    {
        return $user->hasPermission('viewany.task');
    }

    /**
     * Determine whether the user can view the DocTask.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return mixed
     */
    public function view(User $user, Task $task)
    {
		return $user->hasPermission('view.task');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
		return $user->hasPermission('create.task');
    }

    /**
     * Determine whether the user can update the DocTask.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return mixed
     */
    public function update(User $user, Task $task)
    {
        return $user->hasPermission('update.task') || $user->id == ($task->user_id??null);
    }

    /**
     * Determine whether the user can delete the DocTask.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return mixed
     */
    public function delete(User $user, Task $task)
    {
        return  $user->hasPermission('delete.task')||$user->id == ($task->user_id??null);
    }

    /**
     * Determine whether the user can restore the DocTask.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return mixed
     */
    public function restore(User $user, Task $task)
    {
         return $user->hasPermission('restore.task') || $user->id == ($task->user_id??null);
    }

    /**
     * Determine whether the user can permanently delete the DocTask.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Task  $task
     * @return mixed
     */
    public function forceDelete(User $user, Task $task)
    {
        return $user->hasPermission('forcedelete.task') || $user->id == ($task->user_id??null);		
    }
}