<?php

namespace Tests\Feature\V1\GroceryLists;

use App\Models\GroceryList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyGroceryListTest extends TestCase
{
    use RefreshDatabase;

    private $url = '/api/v1/grocery-lists';

    public function test_return_401_authentication_error_for_accessing_the_url(): void
    {
        $response = $this->deleteJson($this->url.'/fake-slug');

        $response->assertUnauthorized()->assertJson(['error' => 'Unauthenticated.']);
    }

    public function test_return_404_not_found_on_non_existing_grocery_list(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // no Grocery List on this slug
        $response = $this->actingAs($user)->deleteJson($this->url.'/non-existing-slug');

        $response->assertNotFound()->assertJson(['error' => __('common.not_found.grocery_list')]);
    }

    public function test_return_404_not_found_when_deleting_grocery_list_by_unauthorize_user(): void
    {
        /** @var User $auth */
        $auth = User::factory()->create();
        $owner = User::factory()->create();
        $owner->defaultMaxGroceryLists()->create();

        $groceryList = GroceryList::factory()->for($owner)->create()->first();

        $url = $this->url.'/'.$groceryList->slug;

        // $auth is not the owner of the grocery list, but still trying to delete it.
        $response = $this->actingAs($auth, 'web')->deleteJson($url);

        $response->assertNotFound()->assertJson(['error' => __('common.not_found.grocery_list')]);

        $this->assertAuthenticated('web');
    }

    public function test_return_204_no_content_when_successfully_deleting_grocery_list(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $groceryList = GroceryList::factory()
            ->for($user)
            ->create([
                'name' => 'New Grocery List',
                'description' => 'New Grocery List Description',
            ])
            ->first();

        $url = $this->url.'/'.$groceryList->slug;

        $response = $this->actingAs($user)->deleteJson($url);

        $response->assertNoContent();

        $this->assertAuthenticated('web');
        $this->assertSoftDeleted('grocery_lists', ['slug' => $groceryList->slug]);
    }
}
