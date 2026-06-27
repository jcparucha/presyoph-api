<?php

namespace App\Services;

use App\Models\GroceryList;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
                // if $published is null, do nothing
                // this will load both private and public grocery list

                if (! is_null($published)) {
                    // load either private or public grocery liest
                    $published ? $query->published() : $query->unpublished();
                }

                $query->orderByDesc('created_at');
            },
            'groceryLists.user',
        ])->groceryLists;
    }

    public function get(GroceryList $groceryList): GroceryList
    {
        $groceryList->load('user');

        // anyone can see the grocery list if it's public
        if ($groceryList->is_public) {
            return $groceryList;
        }

        $authUser = Auth::user();

        // if the grocery list is private, check if it belongs to the auth user
        if ($authUser && $authUser->id === $groceryList->created_by) {
            return $groceryList;
        }

        // else, abort operation
        abort(Response::HTTP_NOT_FOUND, __('common.not_found.grocery_list'));
    }

    public function create(array $data): GroceryList
    {
        $user = Auth::user();

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

    public function delete(GroceryList $groceryList): void
    {
        DB::transaction(function () use ($groceryList) {
            // TODO delete first the items

            // then delete the grocery list
            $groceryList->delete();
        });
    }

    private function validateMaxLimit(User $user)
    {
        $maxGroceryLists = $user->entitlement->max_grocery_lists;

        // check the user's grocery list, max of 3.
        if ($user->groceryLists()->count() >= $maxGroceryLists) {
            throw ValidationException::withMessages([
                'system' => "Maximum grocery list limit reached. You can only create up to $maxGroceryLists.",
            ]);
        }
    }
}
