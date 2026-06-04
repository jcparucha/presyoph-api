<?php

namespace Tests\Feature\GroceryList;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class StoreGroceryListTest extends TestCase
{
    use RefreshDatabase;

    private $url = '/api/v1/users/:id/grocery_lists';

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

    // TODO implement Store logic
    public function test_return_401_when_user_is_not_authenticated(): void
    {
        $user = User::factory()->create();

        $data = [
            'name' => 'Grocery List Name',
            'description' => 'A sample grocery list for testing.',
        ];

        $response = $this->postJson(Str::replace(':id', $user->id, $this->url), $data);

        $response->assertUnauthorized()->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_return_201_created_when_successfully_creating_grocery_list(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // add default entitlement
        $user->defaultMaxGroceryLists()->create();

        $url = Str::replace(':id', $user->id, $this->url);

        $data = [
            'name' => 'Grocery List Name',
            'description' => 'A sample grocery list for testing.',
        ];

        $response = $this->actingAs($user, 'web')->postJson($url, $data);

        $response
            ->assertCreated()
            ->assertHeaderContains('Location', 'http://localhost'.$url)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('links')
                    ->has(
                        'data',
                        fn ($json) => $json
                            ->where('name', $data['name'])
                            ->where('description', $data['description'])
                            ->where('published', false)
                            ->where('created_by', $user->username)
                            ->whereType('id', 'integer')
                            ->whereType('slug', 'string')
                            ->etc(),
                    ),
            );
    }

    public function test_return_200_ok_on_idempotent_post(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // add default entitlement
        $user->defaultMaxGroceryLists()->create();

        $url = Str::replace(':id', $user->id, $this->url);

        $data = [
            'name' => 'Grocery List Name',
            'description' => 'A sample grocery list for testing.',
        ];

        // First Request: Create initial Data then check if 201 Created
        $response1 = $this->actingAs($user, 'web')->postJson($url, $data);

        // Second Request: Create initial Data then check if 201 Created
        $response2 = $this->actingAs($user, 'web')->postJson($url, $data);

        // Assert First Request
        $response1
            ->assertCreated()
            ->assertHeaderContains('Location', 'http://localhost'.$url)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('links')
                    ->has(
                        'data',
                        fn ($json) => $json
                            ->where('name', $data['name'])
                            ->where('description', $data['description'])
                            ->where('published', false)
                            ->where('created_by', $user->username)
                            ->whereType('id', 'integer')
                            ->whereType('slug', 'string')
                            ->etc(),
                    ),
            );

        // Assert Second Request, should return same data, but the status code is 200 OK
        $response2
            ->assertOk()
            ->assertHeaderContains('Location', 'http://localhost'.$url)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('links')
                    ->has(
                        'data',
                        fn ($json) => $json
                            ->where('name', $data['name'])
                            ->where('description', $data['description'])
                            ->where('published', false)
                            ->where('created_by', $user->username)
                            ->whereType('id', 'integer')
                            ->whereType('slug', 'string')
                            ->etc(),
                    ),
            );
    }

    // TODO add test for validation error
    // TODO add validation that the maximum grocery list is 3
}
