<?php

namespace App\Policies;

use App\Models\User;
use App\Models\LineStoDetail;
use Illuminate\Auth\Access\HandlesAuthorization;

class LineStoDetailPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_line::sto::detail');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LineStoDetail $lineStoDetail): bool
    {
        return $user->can('view_line::sto::detail');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_line::sto::detail');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LineStoDetail $lineStoDetail): bool
    {
        // Check if user has permission and is the creator of parent LineSto or has leader role
        if (!$user->can('update_line::sto::detail')) {
            return false;
        }
        
        // Allow if user is the creator of parent LineSto or has leader role
        return $lineStoDetail->lineSto->created_by === $user->name || $user->hasRole('leader');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LineStoDetail $lineStoDetail): bool
    {
        // Check if user has permission and is the creator of parent LineSto or has leader role
        if (!$user->can('delete_line::sto::detail')) {
            return false;
        }
        
        // Allow if user is the creator of parent LineSto or has leader role
        return $lineStoDetail->lineSto->created_by === $user->name || $user->hasRole('leader');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_line::sto::detail');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, LineStoDetail $lineStoDetail): bool
    {
        return $user->can('force_delete_line::sto::detail');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_line::sto::detail');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, LineStoDetail $lineStoDetail): bool
    {
        return $user->can('restore_line::sto::detail');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_line::sto::detail');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, LineStoDetail $lineStoDetail): bool
    {
        return $user->can('replicate_line::sto::detail');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_line::sto::detail');
    }
}
