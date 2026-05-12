<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct() {}

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Product $product): Response
    {
        return $user->id === $product->added_by && !is_null($product->added_by)
            ? Response::allow()
            : Response::denyAsNotFound(
                'You are not authorized to do this action.',
            );
    }
}
