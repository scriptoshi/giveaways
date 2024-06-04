<?php
namespace App\Policies;

use App\Models\Project;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy
{
    use HandlesAuthorization;
	
	public function before(User $user)
	{
		if ($user->isAdmin()) {
			return true;
		}
	}
	
	 /**
     * Determine whether the user can view the DocProject.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Project  $project
     * @return mixed
     */
    public function viewAny(User $user, Project $project)
    {
        return $user->hasPermission('viewany.project');
    }

    /**
     * Determine whether the user can view the DocProject.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Project  $project
     * @return mixed
     */
    public function view(User $user, Project $project)
    {
		return $user->hasPermission('view.project');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
		return $user->hasPermission('create.project');
    }

    /**
     * Determine whether the user can update the DocProject.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Project  $project
     * @return mixed
     */
    public function update(User $user, Project $project)
    {
        return $user->hasPermission('update.project') || $user->id == ($project->user_id??null);
    }

    /**
     * Determine whether the user can delete the DocProject.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Project  $project
     * @return mixed
     */
    public function delete(User $user, Project $project)
    {
        return  $user->hasPermission('delete.project')||$user->id == ($project->user_id??null);
    }

    /**
     * Determine whether the user can restore the DocProject.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Project  $project
     * @return mixed
     */
    public function restore(User $user, Project $project)
    {
         return $user->hasPermission('restore.project') || $user->id == ($project->user_id??null);
    }

    /**
     * Determine whether the user can permanently delete the DocProject.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Project  $project
     * @return mixed
     */
    public function forceDelete(User $user, Project $project)
    {
        return $user->hasPermission('forcedelete.project') || $user->id == ($project->user_id??null);		
    }
}