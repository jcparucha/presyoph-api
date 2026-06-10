<?php

namespace App\Policies;

use App\Models\GroceryList;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class GroceryListPolicy
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
    public function update(User $user, GroceryList $groceryList): Response
    {
        return $user->id === $groceryList->created_by && ! is_null($groceryList->created_by)
            ? Response::allow()
            : Response::denyAsNotFound();
    }
}
