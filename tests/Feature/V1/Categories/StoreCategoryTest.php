<?php

namespace Tests\Feature\V1\Categories;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class StoreCategoryTest extends TestCase
{
    use RefreshDatabase;

    private $url = '/api/v1/categories';

    private $fields = ['name', 'description'];

    public function test_return_401_when_user_is_not_authenticated(): void
    {
        $data = [
            'name' => fake()->word(),
            'description' => fake()->text(),
        ];

        $response = $this->postJson($this->url, $data);

        $response->assertUnauthorized()->assertJson(['error' => 'Unauthenticated.']);
    }

    public function test_return_422_validation_error_payload_required(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response1 = $this->actingAs($user, 'web')->postJson($this->url, []);
        $response2 = $this->actingAs($user, 'web')->postJson($this->url, ['name' => '']);

        $response1->assertUnprocessable()->assertJsonValidationErrors($this->fields, 'errors');
        $response2->assertUnprocessable()->assertJsonValidationErrors($this->fields, 'errors');
    }

    public function test_return_422_validation_error_payload_min_max_characters(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // minimum of 3 characters
        $response1 = $this->actingAs($user, 'web')->postJson($this->url, ['name' => 'as', 'description' => 'as']);
        // maximimum of 50 and 255 characters
        $response2 = $this->actingAs($user, 'web')->postJson($this->url, [
            'name' => fake()->realTextBetween(50, 60),
            'description' => fake()->paragraph(260, 270),
        ]);

        $response1->assertUnprocessable()->assertJsonValidationErrors($this->fields, 'errors');
        $response2->assertUnprocessable()->assertJsonValidationErrors($this->fields, 'errors');
    }

    public function test_return_422_validation_error_payload_unsupported_characters(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'web')->postJson($this->url, [
            'name' => 'Brand 1 (***)',
            'description' => 'Some long text with invalid character *^* ~_~',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors($this->fields, 'errors');
    }

    public function test_return_201_created_when_successfully_creating_category(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $data = [
            'name' => fake()->word(),
            'description' => fake()->text(),
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
                            ->whereType('id', 'integer')
                            ->whereType('slug', 'string'),
                    ),
            );

        $this->assertDatabaseHas('categories', $data);
    }

    public function test_return_200_ok_on_idempotent_post(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // add default entitlement
        $user->defaultMaxGroceryLists()->create();

        $data = [
            'name' => 'Category',
            'description' => 'Lorem Ipsum Dolor',
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
                            ->whereType('id', 'integer')
                            ->whereType('slug', 'string'),
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
                            ->whereType('id', 'integer')
                            ->whereType('slug', 'string'),
                    ),
            );

        $this->assertDatabaseHas('categories', $data);
    }
}
