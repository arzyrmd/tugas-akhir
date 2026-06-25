<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\CustomProductRequest;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomProductRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the admin can view any models.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function viewAny(Admin $admin): bool
    {
        return $admin->can('view_any_custom::product::request');
    }

    /**
     * Determine whether the admin can view the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\CustomProductRequest  $customProductRequest
     * @return bool
     */
    public function view(Admin $admin, CustomProductRequest $customProductRequest): bool
    {
        return $admin->can('view_custom::product::request');
    }

    /**
     * Determine whether the admin can create models.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function create(Admin $admin): bool
    {
        return $admin->can('create_custom::product::request');
    }

    /**
     * Determine whether the admin can update the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\CustomProductRequest  $customProductRequest
     * @return bool
     */
    public function update(Admin $admin, CustomProductRequest $customProductRequest): bool
    {
        return $admin->can('update_custom::product::request');
    }

    /**
     * Determine whether the admin can delete the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\CustomProductRequest  $customProductRequest
     * @return bool
     */
    public function delete(Admin $admin, CustomProductRequest $customProductRequest): bool
    {
        return $admin->can('delete_custom::product::request');
    }

    /**
     * Determine whether the admin can bulk delete.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function deleteAny(Admin $admin): bool
    {
        return $admin->can('delete_any_custom::product::request');
    }

    /**
     * Determine whether the admin can permanently delete.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\CustomProductRequest  $customProductRequest
     * @return bool
     */
    public function forceDelete(Admin $admin, CustomProductRequest $customProductRequest): bool
    {
        return $admin->can('force_delete_custom::product::request');
    }

    /**
     * Determine whether the admin can permanently bulk delete.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function forceDeleteAny(Admin $admin): bool
    {
        return $admin->can('force_delete_any_custom::product::request');
    }

    /**
     * Determine whether the admin can restore.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\CustomProductRequest  $customProductRequest
     * @return bool
     */
    public function restore(Admin $admin, CustomProductRequest $customProductRequest): bool
    {
        return $admin->can('restore_custom::product::request');
    }

    /**
     * Determine whether the admin can bulk restore.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function restoreAny(Admin $admin): bool
    {
        return $admin->can('restore_any_custom::product::request');
    }

    /**
     * Determine whether the admin can replicate.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\CustomProductRequest  $customProductRequest
     * @return bool
     */
    public function replicate(Admin $admin, CustomProductRequest $customProductRequest): bool
    {
        return $admin->can('replicate_custom::product::request');
    }

    /**
     * Determine whether the admin can reorder.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function reorder(Admin $admin): bool
    {
        return $admin->can('reorder_custom::product::request');
    }

}
