<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Category $category): Response
    {
        return $user->id === $category->added_by && ! is_null($category->added_by)
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
