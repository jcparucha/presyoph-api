<?php

namespace App\Services;

use App\Models\GroceryList;
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

    public function all(?bool $published, User $user): Collection
    {
        return $user->load([
            'groceryLists' => function ($query) use ($published) {
                if (is_null($published)) {
                    return;
                }

                $published ? $query->published() : $query->unpublished();
            },
            'groceryLists.user',
        ])->groceryLists;
    }

    public function get(GroceryList $groceryList): GroceryList
    {
        return $groceryList->load('user');
    }
}
