<?php

namespace App\Policies;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BrandPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Brand $brand): Response
    {
        return $user->id === $brand->added_by && ! is_null($brand->added_by)
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
