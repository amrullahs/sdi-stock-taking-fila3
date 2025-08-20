<?php

namespace App\Policies;

use App\Models\User;
use App\Models\StockTaking;
use Illuminate\Auth\Access\HandlesAuthorization;

class StockTakingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_stock::taking');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StockTaking $stockTaking): bool
    {
        return $user->can('view_stock::taking');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_stock::taking');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StockTaking $stockTaking): bool
    {
        // Super admin can update any stock taking
        if ($user->hasRole('super_admin')) {
            return $user->can('update_stock::taking');
        }
        
        // Regular users can only update stock taking they created
        return $user->can('update_stock::taking') && $stockTaking->sto_user === $user->name;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StockTaking $stockTaking): bool
    {
        // Super admin can delete any stock taking
        if ($user->hasRole('super_admin')) {
            return $user->can('delete_stock::taking');
        }
        
        // Regular users can only delete stock taking they created
        return $user->can('delete_stock::taking') && $stockTaking->sto_user === $user->name;
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        // Only super admin can bulk delete to enforce individual ownership checks for regular users
        return $user->hasRole('super_admin') && $user->can('delete_any_stock::taking');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, StockTaking $stockTaking): bool
    {
        return $user->can('force_delete_stock::taking');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_stock::taking');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, StockTaking $stockTaking): bool
    {
        return $user->can('restore_stock::taking');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_stock::taking');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, StockTaking $stockTaking): bool
    {
        return $user->can('replicate_stock::taking');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_stock::taking');
    }
}
