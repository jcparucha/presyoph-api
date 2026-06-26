<?php

namespace Tests\Feature\GroceryList;

use App\Models\GroceryList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetGroceryListTest extends TestCase
{
    use RefreshDatabase;

    private $url = '/api/v1/grocery-lists';

    private $dataStructure = [
        'id',
        'name',
        'slug',
        'description',
        'published',
        'created_at',
        'updated_at',
        'created_by',
    ];

    public function test_return_401_authentication_error_for_accessing_the_url(): void
    {
        $response = $this->getJson($this->url);

        $response->assertUnauthorized()->assertJson(['error' => 'Unauthenticated.']);
    }

    public function test_return_200_ok_user_empty_grocery_lists(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson($this->url);

        $response->assertOk()->assertExactJson(['data' => []]);
    }

    public function test_return_200_ok_user_all_grocery_lists(): void
    {
        /** @var User $user */
        $user = User::factory()
            ->has(
                GroceryList::factory()
                    ->count(4)
                    ->sequence(['is_public' => false], ['is_public' => true]),
                'groceryLists',
            )
            ->create();

        $response = $this->actingAs($user)->getJson($this->url);

        $response
            ->assertOk()
            ->assertJsonStructure(['data' => [$this->dataStructure]])
            ->assertJson(fn (AssertableJson $json) => $json->has('data', 4));
    }

    public function test_return_200_ok_user_unpublished_grocery_lists(): void
    {
        /** @var User $user */
        $user = User::factory()
            ->has(
                GroceryList::factory()
                    ->count(4)
                    ->sequence(['is_public' => false], ['is_public' => true]),
                'groceryLists',
            )
            ->create();

        $response = $this->actingAs($user)->getJson($this->url.'?published=false');

        $response
            ->assertOk()
            ->assertJsonStructure(['data' => [$this->dataStructure]])
            ->assertJson(
                fn (AssertableJson $json) => $json->has(
                    'data',
                    2,
                    fn (AssertableJson $json) => $json->where('published', false)->etc(),
                ),
            );
    }

    public function test_return_200_ok_user_published_grocery_lists(): void
    {
        /** @var User $user */
        $user = User::factory()
            ->has(
                GroceryList::factory()
                    ->count(4)
                    ->sequence(['is_public' => false], ['is_public' => true]),
                'groceryLists',
            )
            ->create();

        $response = $this->actingAs($user)->getJson($this->url.'?published=true');

        $response
            ->assertOk()
            ->assertJsonStructure(['data' => [$this->dataStructure]])
            ->assertJson(
                fn (AssertableJson $json) => $json->has(
                    'data',
                    2,
                    fn (AssertableJson $json) => $json->where('published', true)->etc(),
                ),
            );
    }

    public function test_return_422_validation_error_for_incorrect_published_value(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson($this->url.'?published=yes');

        $response->assertUnprocessable()->assertJson(['message' => 'The published field must be true or false.']);
    }

    public function test_return_200_ok_user_private_or_public_grocery_list(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $groceryList = GroceryList::factory()
            ->count(2)
            ->sequence(
                ['name' => 'Public Grocery List', 'is_public' => true],
                ['name' => 'Private Grocery List', 'is_public' => false],
            )
            ->for($user)
            ->create();

        $public = $groceryList->first();
        $private = $groceryList->last();

        // user is authenticated
        $response1 = $this->actingAs($user)->getJson($this->url.'/'.$public->slug);
        $response2 = $this->actingAs($user)->getJson($this->url.'/'.$private->slug);

        $response1
            ->assertOk()
            ->assertJsonStructure(['data' => $this->dataStructure])
            ->assertJson([
                'data' => [
                    'id' => $public->id,
                    'name' => 'Public Grocery List',
                    'slug' => $public->slug,
                    'description' => $public->description,
                    'published' => true, // should be public
                    'created_at' => $public->created_at->toISOString(),
                    'updated_at' => $public->updated_at->toISOString(),
                    'created_by' => $user->username,
                ],
            ]);

        $response2
            ->assertOk()
            ->assertJsonStructure(['data' => $this->dataStructure])
            ->assertJson([
                'data' => [
                    'id' => $private->id,
                    'name' => 'Private Grocery List',
                    'slug' => $private->slug,
                    'description' => $private->description,
                    'published' => false, // should be private
                    'created_at' => $private->created_at->toISOString(),
                    'updated_at' => $private->updated_at->toISOString(),
                    'created_by' => $user->username,
                ],
            ]);
    }

    public function test_return_200_ok_user_specific_public_grocery_list(): void
    {
        /** @var User $auth */
        $auth = User::factory()->create();

        /** @var User $owner */
        $owner = User::factory()
            ->has(GroceryList::factory()->state(fn () => ['name' => 'Public Grocery List', 'is_public' => true]))
            ->create();

        $groceryList = $owner->groceryLists->first();

        $data = [
            'id' => $groceryList->id,
            'name' => 'Public Grocery List',
            'slug' => $groceryList->slug,
            'description' => $groceryList->description,
            'published' => true, // should be public
            'created_at' => $groceryList->created_at->toISOString(),
            'updated_at' => $groceryList->updated_at->toISOString(),
            'created_by' => $owner->username,
        ];

        // the $auth is authenticated and is accessing the $user public grocery list
        $response1 = $this->actingAs($auth)->getJson($this->url.'/'.$groceryList->slug);
        // no authenticated user
        $response2 = $this->getJson($this->url.'/'.$groceryList->slug);

        $response1
            ->assertOk()
            ->assertJsonStructure(['data' => $this->dataStructure])
            ->assertJson([
                'data' => $data,
            ]);

        $response2
            ->assertOk()
            ->assertJsonStructure(['data' => $this->dataStructure])
            ->assertJson([
                'data' => $data,
            ]);
    }

    public function test_return_404_not_found_on_user_private_grocery_list(): void
    {
        /** @var User $auth */
        $auth = User::factory()->create();

        /** @var User $owner */
        $owner = User::factory()
            ->has(GroceryList::factory()->state(fn () => ['name' => 'Private Grocery List', 'is_public' => false]))
            ->create();

        $groceryList = $owner->groceryLists->first();

        // $auth is trying to access the $owner's private grocery list
        $response = $this->actingAs($auth)->getJson($this->url.'/'.$groceryList->slug);

        $response->assertNotFound()->assertJson(['error' => __('common.not_found.grocery_list')]);
    }

    public function test_return_404_not_found_on_user_non_existing_grocery_list(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson($this->url.'/non-existing-slug');

        $response->assertNotFound()->assertJson(['error' => __('common.not_found.grocery_list')]);
    }

    public function test_return_200_ok_specific_user_public_grocery_lists(): void
    {
        $url = '/api/v1/users/:username/grocery-lists';

        /** @var User $user */
        $user = User::factory()
            ->has(
                GroceryList::factory()
                    ->count(3)
                    ->sequence(['is_public' => false], ['is_public' => true]),
                'groceryLists',
            )
            ->create();

        $response = $this->getJson(Str::replace(':username', $user->username, $url));

        $response
            ->assertOk()
            ->assertJsonStructure(['data' => [$this->dataStructure]])
            ->assertJson(
                fn (AssertableJson $json) => $json->has(
                    'data',
                    1,
                    fn (AssertableJson $json) => $json->where('published', true)->etc(),
                ),
            );
    }
}
