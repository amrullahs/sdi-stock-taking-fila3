<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LineSto;
use Illuminate\Auth\Access\HandlesAuthorization;

class LineStoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_line::sto');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LineSto $lineSto): bool
    {
        return $user->can('view_line::sto');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_line::sto');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LineSto $lineSto): bool
    {
        // Super admin can update anything
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Check if user has basic update permission
        if (!$user->can('update_line::sto')) {
            return false;
        }

        // If user owns the record, they can update it (handle string/int comparison)
        if ($lineSto->created_by == $user->id) {
            return true;
        }

        // If user has permission to update others' records
        return $user->can('update_others_line::sto');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LineSto $lineSto): bool
    {
        // Super admin can delete anything
        if ($user->hasRole('super-admin')) {
            return true;
        }

        // Check if user has basic delete permission
        if (!$user->can('delete_line::sto')) {
            return false;
        }

        // If user owns the record, they can delete it (handle string/int comparison)
        if ($lineSto->created_by == $user->id) {
            return true;
        }

        // If user has permission to delete others' records
        return $user->can('delete_others_line::sto');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_line::sto');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, LineSto $lineSto): bool
    {
        return $user->can('force_delete_line::sto');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_line::sto');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, LineSto $lineSto): bool
    {
        return $user->can('restore_line::sto');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_line::sto');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, LineSto $lineSto): bool
    {
        return $user->can('replicate_line::sto');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_line::sto');
    }
}
