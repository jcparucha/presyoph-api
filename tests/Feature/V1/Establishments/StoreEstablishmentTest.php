<?php

namespace Tests\Feature\V1\Establishments;

use App\Models\Barangay;
use App\Models\StoreType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class StoreEstablishmentTest extends TestCase
{
    use RefreshDatabase;

    private User $auth;

    private $url = '/api/v1/establishments';

    protected function setUp(): void
    {
        parent::setUp();

        $this->auth = User::factory()->create();
    }

    public function test_return_401_when_user_is_not_authenticated(): void
    {
        $data = [
            'name' => 'Lorem Ipsum Supermarket',
            'barangay_code' => '1234567890',
            'store_type' => 'Supermall',
        ];

        $response = $this->postJson($this->url, $data);

        $response->assertUnauthorized()->assertJson(['error' => 'Unauthenticated.']);
    }

    public function test_return_422_validation_error_payload_required(): void
    {
        $keysWithErrors = ['name', 'barangay_code', 'store_type'];

        $response1 = $this->actingAs($this->auth, 'web')->postJson($this->url, []);
        $response2 = $this->actingAs($this->auth, 'web')->postJson($this->url, [
            'name' => '',
            'barangay_code' => '',
            'store_type' => '',
        ]);

        $response1->assertUnprocessable()->assertJsonValidationErrors($keysWithErrors, 'errors');
        $response2->assertUnprocessable()->assertJsonValidationErrors($keysWithErrors, 'errors');
    }

    public function test_return_422_validation_error_payload_min_max_characters(): void
    {
        // Assert min characters is 3
        $response1 = $this->actingAs($this->auth, 'web')->postJson($this->url, ['name' => 'as']);
        // assert max characters is name=50
        $response2 = $this->actingAs($this->auth, 'web')->postJson($this->url, [
            'name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        ]);

        $response1->assertUnprocessable()->assertJsonValidationErrorFor('name', 'errors');
        $response2->assertUnprocessable()->assertJsonValidationErrorFor('name', 'errors');
    }

    public function test_return_422_validation_error_payload_unsupported_special_characters(): void
    {
        $response = $this->actingAs($this->auth, 'web')->postJson($this->url, [
            'name' => 'Test ^_^',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrorFor('name', 'errors');
    }

    public function test_return_422_validation_error_payload_must_be_a_string(): void
    {
        $response = $this->actingAs($this->auth, 'web')->postJson($this->url, [
            'name' => ['test'],
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrorFor('name', 'errors');
    }

    public function test_return_422_validation_error_non_existing_barangay_code_payload(): void
    {
        // create a record first
        $storeType = StoreType::factory()->create();

        $response = $this->actingAs($this->auth, 'web')->postJson($this->url, [
            'name' => 'Lorem Ipsum Supermall',
            'store_type' => $storeType->name,
            'barangay_code' => '1234567890',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrorFor('barangay_code', 'errors');
    }

    public function test_return_422_validation_error_non_existing_store_type_payload(): void
    {
        // create a record first
        $barangay = Barangay::factory()->create();

        $response = $this->actingAs($this->auth, 'web')->postJson($this->url, [
            'name' => 'Lorem Ipsum Supermall',
            'store_type' => 'non existing store type',
            'barangay_code' => $barangay->code,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrorFor('store_type', 'errors');
    }

    public function test_return_201_created_when_successfully_creating_establishment(): void
    {
        // create a record first
        $barangay = Barangay::factory()->create();
        $storeType = StoreType::factory()->create();

        $data = [
            'name' => 'Lorem Ipsum Supermall',
            'store_type' => $storeType->name,
            'barangay_code' => $barangay->code,
        ];

        $response = $this->actingAs($this->auth, 'web')->postJson($this->url, $data);

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
                            ->has(
                                'store_type',
                                fn (AssertableJson $json) => $json->where('name', $storeType->name)->etc(),
                            )
                            ->has(
                                'address',
                                fn (AssertableJson $json) => $json->where('barangay', $barangay->name)->etc(),
                            )
                            ->etc(),
                    ),
            );

        $this->assertDatabaseHas('establishments', [
            'name' => $data['name'],
            'store_type_id' => $storeType->id,
            'barangay_code' => $barangay->code,
        ]);
    }

    public function test_return_200_ok_on_idempotent_post(): void
    {
        $barangay = Barangay::factory()->create();
        $storeType = StoreType::factory()->create();

        $data = [
            'name' => 'Lorem Ipsum Supermall',
            'store_type' => $storeType->name,
            'barangay_code' => $barangay->code,
        ];

        // First Request: Create initial Data then check if 201 Created
        $response1 = $this->actingAs($this->auth, 'web')->postJson($this->url, $data);

        // Second Request: Resend initial Data then check if 200 Ok
        $response2 = $this->actingAs($this->auth, 'web')->postJson($this->url, $data);

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
                            ->has(
                                'store_type',
                                fn (AssertableJson $json) => $json->where('name', $storeType->name)->etc(),
                            )
                            ->has(
                                'address',
                                fn (AssertableJson $json) => $json->where('barangay', $barangay->name)->etc(),
                            )
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
                            ->has(
                                'store_type',
                                fn (AssertableJson $json) => $json->where('name', $storeType->name)->etc(),
                            )
                            ->has(
                                'address',
                                fn (AssertableJson $json) => $json->where('barangay', $barangay->name)->etc(),
                            )
                            ->etc(),
                    ),
            );

        $this->assertDatabaseHas('establishments', [
            'name' => $data['name'],
            'store_type_id' => $storeType->id,
            'barangay_code' => $barangay->code,
        ]);
    }
}
