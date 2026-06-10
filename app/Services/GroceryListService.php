<?php

namespace App\Services;

use App\Models\GroceryList;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class GroceryListService
{
    private $fields = ['name', 'description'];

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

    public function create(array $data, User $user): GroceryList
    {
        $this->validateMaxLimit($user);

        $groceryList = $user->unpublishedGroceryList()->firstOrCreate([
            'name' => $data['name'],
            'description' => $data['description'],
        ]);

        return $groceryList->load('user');
    }

    public function update(array $inputs, GroceryList $groceryList): GroceryList
    {
        foreach ($this->fields as $field) {
            if (isset($inputs[$field]) && $inputs[$field] !== $groceryList->$field) {
                // update slug first if name was changed
                if ($field === 'name' && Str::lower($groceryList->$field) !== Str::lower($inputs[$field])) {
                    $groceryList->slug = generate_unique_slug($inputs[$field]);
                }

                $groceryList->$field = $inputs[$field];
            }
        }

        if ($groceryList->isDirty()) {
            $groceryList->save();
        }

        return $groceryList->load('user');
    }

    private function validateMaxLimit(User $user)
    {
        $maxGroceryLists = $user->entitlement->max_grocery_lists;

        // check the user's grocery list, max of 3.
        if ($user->groceryLists->count() >= $maxGroceryLists) {
            throw ValidationException::withMessages([
                'system' => "Maximum grocery list limit reached. You can only create up to $maxGroceryLists.",
            ]);
        }
    }
}
