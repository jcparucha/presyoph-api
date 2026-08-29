<?php

namespace Tests\Feature\V1\Brands;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class StoreBrandTest extends TestCase
{
    use RefreshDatabase;

    private $url = '/api/v1/brands';

    public function test_return_401_when_user_is_not_authenticated(): void
    {
        $data = ['name' => 'New Brand'];

        $response = $this->postJson($this->url, $data);

        $response->assertUnauthorized()->assertJson(['error' => 'Unauthenticated.']);
    }

    public function test_return_422_validation_error_payload_required(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response1 = $this->actingAs($user, 'web')->postJson($this->url, []);
        $response2 = $this->actingAs($user, 'web')->postJson($this->url, ['name' => '']);

        $response1->assertUnprocessable()->assertJsonValidationErrors('name', 'errors');
        $response2->assertUnprocessable()->assertJsonValidationErrors('name', 'errors');
    }

    public function test_return_422_validation_error_payload_min_max_characters(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response1 = $this->actingAs($user, 'web')->postJson($this->url, ['name' => 'as']);
        $response2 = $this->actingAs($user, 'web')->postJson($this->url, [
            'name' => 'qwertyuiopasdfghjkl qwertyuiopasdfghjkl zxcvbnmzxcvbnm',
        ]);

        $response1->assertUnprocessable()->assertJsonValidationErrors('name', 'errors');
        $response2->assertUnprocessable()->assertJsonValidationErrors('name', 'errors');
    }

    public function test_return_422_validation_error_payload_unsupported_characters(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'web')->postJson($this->url, ['name' => 'Brand 1 (***)']);

        $response->assertUnprocessable()->assertJsonValidationErrors('name', 'errors');
    }

    public function test_return_201_created_when_successfully_creating_brand(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $data = ['name' => 'New Brand 1'];

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
                            ->whereType('id', 'integer')
                            ->whereType('slug', 'string'),
                    ),
            );
    }

    public function test_return_200_ok_on_idempotent_post(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        // add default entitlement
        $user->defaultMaxGroceryLists()->create();

        $data = ['name' => 'New Brand 2'];

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
                            ->whereType('id', 'integer')
                            ->whereType('slug', 'string')
                            ->etc(),
                    ),
            );
    }
}
