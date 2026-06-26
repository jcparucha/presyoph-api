<?php

namespace Tests\Feature\GroceryList;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class StoreGroceryListTest extends TestCase
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

    public function test_return_401_when_user_is_not_authenticated(): void
    {
        $data = [
            'name' => 'Grocery List Name',
            'description' => 'A sample grocery list for testing.',
        ];

        $response = $this->postJson($this->url, $data);

        $response->assertUnauthorized()->assertJson(['error' => 'Unauthenticated.']);
    }

    public function test_return_422_validation_error_payload_required(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $keysWithErrors = ['name', 'description'];

        $response1 = $this->actingAs($user, 'web')->postJson($this->url, []);
        $response2 = $this->actingAs($user, 'web')->postJson($this->url, ['name' => '', 'description' => '']);

        $response1->assertUnprocessable()->assertJsonValidationErrors($keysWithErrors, 'errors');
        $response2->assertUnprocessable()->assertJsonValidationErrors($keysWithErrors, 'errors');
    }

    public function test_return_422_validation_error_payload_min_max_characters(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $keysWithErrors = ['name', 'description'];

        // Assert min characters is 3
        $response1 = $this->actingAs($user, 'web')->postJson($this->url, ['name' => 'as', 'description' => 'df']);
        // assert max characters is name=50, description=255
        $response2 = $this->actingAs($user, 'web')->postJson($this->url, [
            'name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'description' => 'Ut quis mollis massa. Pellentesque habitant morbi tristique senectus et netus et 
            malesuada fames ac turpis egestas. Quisque blandit condimentum odio, volutpat posuere nisl auctor vel. 
            Mauris id quam tristique metus pulvinar aliquet ut a ligula. Fusce sagittis interdum laoreet.',
        ]);

        $response1->assertUnprocessable()->assertJsonValidationErrors($keysWithErrors, 'errors');
        $response2->assertUnprocessable()->assertJsonValidationErrors($keysWithErrors, 'errors');
    }

    public function test_return_422_validation_error_payload_unsupported_special_characters(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'web')->postJson($this->url, [
            'name' => 'Test ^_^',
            'description' => 'asdf ^_^',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['name', 'description'], 'errors');
    }

    public function test_return_422_validation_error_maximum_grocery_lists_reached(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // add default entitlement
        $user->defaultMaxGroceryLists()->create();

        $data = [
            'name' => 'P500 Noche Buena Challenge',
            'description' => 'Worth of Noche Buena groceries',
        ];

        $response1 = $this->actingAs($user, 'web')->postJson($this->url, $data);
        $response2 = $this->actingAs($user, 'web')->postJson($this->url, [
            ...$data,
            'name' => 'P500 Christmas challenge',
        ]);
        $response3 = $this->actingAs($user, 'web')->postJson($this->url, [
            ...$data,
            'name' => 'P1000 Christmas Budget',
        ]);
        $response4 = $this->actingAs($user, 'web')->postJson($this->url, [
            ...$data,
            'description' => 'This will error because of validateMaxLimit() method.',
        ]);

        $response1->assertCreated();
        $response2->assertCreated();
        $response3->assertCreated();
        $response4->assertUnprocessable()->assertJsonValidationErrorFor('system', 'errors');
    }

    public function test_return_422_validation_error_payload_must_be_a_string(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $user->defaultMaxGroceryLists()->create();

        $response = $this->actingAs($user, 'web')->postJson($this->url, [
            'name' => ['test'],
            'description' => [],
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['name', 'description'], 'errors');
    }

    public function test_return_201_created_when_successfully_creating_grocery_list(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // add default entitlement
        $user->defaultMaxGroceryLists()->create();

        $data = [
            'name' => 'Grocery List Name',
            'description' => 'A sample grocery list for testing.',
        ];

        $response = $this->actingAs($user, 'web')->postJson($this->url, $data);

        $response
            ->assertCreated()
            ->assertHeaderContains('Location', 'http://localhost'.$this->url)
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

        $data = [
            'name' => 'Grocery List Name',
            'description' => 'A sample grocery list for testing.',
        ];

        // First Request: Create initial Data then check if 201 Created
        $response1 = $this->actingAs($user, 'web')->postJson($this->url, $data);

        // Second Request: Resend initial Data then check if 200 Ok
        $response2 = $this->actingAs($user, 'web')->postJson($this->url, $data);

        // Assert First Request
        $response1
            ->assertCreated()
            ->assertHeaderContains('Location', 'http://localhost'.$this->url)
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
            ->assertHeaderContains('Location', 'http://localhost'.$this->url)
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
}
