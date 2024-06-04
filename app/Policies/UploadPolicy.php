<?php
namespace App\Policies;

use App\Models\Upload;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UploadPolicy
{
    use HandlesAuthorization;
	
	public function before(User $user)
	{
		if ($user->isAdmin()) {
			return true;
		}
	}
	
	 /**
     * Determine whether the user can view the DocUpload.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Upload  $upload
     * @return mixed
     */
    public function viewAny(User $user, Upload $upload)
    {
        return $user->hasPermission('viewany.upload');
    }

    /**
     * Determine whether the user can view the DocUpload.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Upload  $upload
     * @return mixed
     */
    public function view(User $user, Upload $upload)
    {
		return $user->hasPermission('view.upload');
    }

    /**
     * Determine whether the user can create DocDummyPluralModel.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
		return $user->hasPermission('create.upload');
    }

    /**
     * Determine whether the user can update the DocUpload.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Upload  $upload
     * @return mixed
     */
    public function update(User $user, Upload $upload)
    {
        return $user->hasPermission('update.upload') || $user->id == ($upload->user_id??null);
    }

    /**
     * Determine whether the user can delete the DocUpload.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Upload  $upload
     * @return mixed
     */
    public function delete(User $user, Upload $upload)
    {
        return  $user->hasPermission('delete.upload')||$user->id == ($upload->user_id??null);
    }

    /**
     * Determine whether the user can restore the DocUpload.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Upload  $upload
     * @return mixed
     */
    public function restore(User $user, Upload $upload)
    {
         return $user->hasPermission('restore.upload') || $user->id == ($upload->user_id??null);
    }

    /**
     * Determine whether the user can permanently delete the DocUpload.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Upload  $upload
     * @return mixed
     */
    public function forceDelete(User $user, Upload $upload)
    {
        return $user->hasPermission('forcedelete.upload') || $user->id == ($upload->user_id??null);		
    }
}