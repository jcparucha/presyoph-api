<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class GroceryListService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function all(User $user): Collection
    {
        return $user->load('groceryLists.user')->groceryLists;
    }
}
