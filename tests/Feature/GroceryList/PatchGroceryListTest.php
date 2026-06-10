<?php

namespace Tests\Feature\GroceryList;

use App\Models\GroceryList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class PatchGroceryListTest extends TestCase
{
    use RefreshDatabase;

    private $url = '/api/v1/users/:id/groceryLists';

    public function test_return_401_unauthorized_when_user_is_not_authenticated(): void
    {
        $user = User::factory()->create();
        $url = Str::replace(':id', $user->id, $this->url);

        $response = $this->patchJson($url.'/1');

        $response->assertUnauthorized()->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_return_404_not_found_when_grocery_list_does_not_exists(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $url = Str::replace(':id', $user->id, $this->url);

        // no Grocery List with an ID of 1...
        $response = $this->actingAs($user)->patchJson($url.'/1', [
            'name' => 'Test Name',
            'description' => 'Test Description',
        ]);

        $response->assertNotFound()->assertJson(['error' => 'GroceryList not found.']);
    }

    public function test_return_validation_error_payload_min_max_characters(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->defaultMaxGroceryLists()->create();
        $groceryList = GroceryList::factory()->count(1)->for($user)->create();
        $url = Str::replace(':id', $user->id, $this->url).'/'.$groceryList->first()->id;
        $keysWithErrors = ['name', 'description'];

        // Assert min characters is 3
        $response1 = $this->actingAs($user, 'web')->patchJson($url, [
            'name' => 'as',
            'description' => 'df',
        ]);
        // assert max characters is name=50, description=255
        $response2 = $this->actingAs($user, 'web')->patchJson($url, [
            'name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'description' => 'Ut quis mollis massa. Pellentesque habitant morbi tristique senectus et netus et 
            malesuada fames ac turpis egestas. Quisque blandit condimentum odio, volutpat posuere nisl auctor vel. 
            Mauris id quam tristique metus pulvinar aliquet ut a ligula. Fusce sagittis interdum laoreet.',
        ]);

        $response1->assertUnprocessable()->assertJsonValidationErrors($keysWithErrors, 'errors');
        $response2->assertUnprocessable()->assertJsonValidationErrors($keysWithErrors, 'errors');
    }

    public function test_return_validation_error_payload_unsupported_special_characters(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->defaultMaxGroceryLists()->create();
        $groceryList = GroceryList::factory()->count(1)->for($user)->create();
        $url = Str::replace(':id', $user->id, $this->url).'/'.$groceryList->first()->id;

        $response = $this->actingAs($user, 'web')->patchJson($url, [
            'name' => 'Test ^_^',
            'description' => 'asdf ^_^',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['name', 'description'], 'errors');
    }

    public function test_return_validation_error_payload_must_be_a_string(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->defaultMaxGroceryLists()->create();
        $groceryList = GroceryList::factory()->count(1)->for($user)->create();
        $url = Str::replace(':id', $user->id, $this->url).'/'.$groceryList->first()->id;

        $response = $this->actingAs($user, 'web')->patchJson($url, [
            'name' => ['test'],
            'description' => [],
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['name', 'description'], 'errors');
    }

    public function test_return_200_ok_when_successfully_updating_grocery_list(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->defaultMaxGroceryLists()->create();
        $groceryList = GroceryList::factory()->count(1)->for($user)->create();
        $url = Str::replace(':id', $user->id, $this->url).'/'.$groceryList->first()->id;
        $newData = ['name' => 'New Grocery List', 'description' => 'New Grocery List Description'];

        $response = $this->actingAs($user, 'web')->patchJson($url, $newData);

        $response
            ->assertStatus(200)
            ->assertJson(
                fn (AssertableJson $json) => $json->has(
                    'data',
                    fn ($json) => $json
                        ->where('name', $newData['name'])
                        ->where('description', $newData['description'])
                        ->etc(),
                ),
            );

        $this->assertAuthenticated('web');
    }

    public function test_return_404_not_found_when_updating_grocery_list_by_unauthorize_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $owner->defaultMaxGroceryLists()->create();
        $groceryList = GroceryList::factory()->count(1)->for($owner)->create();

        $url = Str::replace(':id', $user->id, $this->url).'/'.$groceryList->first()->id;

        $newData = ['name' => 'New Grocery List', 'description' => 'New Grocery List Description'];

        // $user is not the owner of the post, but trying to update it.
        $response = $this->actingAs($user, 'web')->patchJson($url, [
            'name' => $newData,
        ]);

        $response->assertNotFound()->assertJson(['error' => 'GroceryList not found.']);

        $this->assertAuthenticated('web');
    }
}
