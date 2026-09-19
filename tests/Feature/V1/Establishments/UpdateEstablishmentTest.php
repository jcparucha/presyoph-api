<?php

namespace Tests\Feature\V1\GroceryLists;

use App\Models\Barangay;
use App\Models\Establishment;
use App\Models\StoreType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateEstablishmentTest extends TestCase
{
    use RefreshDatabase;

    private User $auth;

    private Establishment $establishment;

    private $url = '/api/v1/establishments/';

    protected function setUp(): void
    {
        parent::setUp();

        $this->auth = User::factory()->create();
        $this->establishment = Establishment::factory()->for($this->auth)->create();
    }

    public function test_return_401_when_user_is_not_authenticated(): void
    {
        $response = $this->patchJson($this->url.$this->establishment->id, ['name' => 'Lorem Ipsum Supermarket']);

        $response->assertUnauthorized()->assertJson(['error' => 'Unauthenticated.']);
    }

    public function test_return_404_not_found_on_non_existing_establishment(): void
    {
        // no Grocery List on this slug
        $response = $this->actingAs($this->auth)->patchJson($this->url.'999999', [
            'name' => 'Test Name',
        ]);

        $response->assertNotFound()->assertJson(['error' => __('common.not_found.establishment')]);
    }

    public function test_return_422_validation_error_payload_required(): void
    {
        $payload = ['name' => '', 'barangay_code' => '', 'store_type' => ''];

        $response = $this->actingAs($this->auth, 'web')->patchJson($this->url.$this->establishment->id, $payload);

        $response->assertUnprocessable()->assertJsonValidationErrors(array_keys($payload), 'errors');
    }

    public function test_return_422_validation_error_payload_min_max_characters(): void
    {
        // Assert min characters is 3
        $response1 = $this->actingAs($this->auth, 'web')->patchJson($this->url.$this->establishment->id, [
            'name' => 'as',
        ]);
        // assert max characters is name=50
        $response2 = $this->actingAs($this->auth, 'web')->patchJson($this->url.$this->establishment->id, [
            'name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
        ]);

        $response1->assertUnprocessable()->assertJsonValidationErrorFor('name', 'errors');
        $response2->assertUnprocessable()->assertJsonValidationErrorFor('name', 'errors');
    }

    public function test_return_422_validation_error_payload_unsupported_special_characters(): void
    {
        $response = $this->actingAs($this->auth, 'web')->patchJson($this->url.$this->establishment->id, [
            'name' => 'Test ^_^',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrorFor('name', 'errors');
    }

    public function test_return_422_validation_error_payload_must_be_a_string(): void
    {
        $response = $this->actingAs($this->auth, 'web')->patchJson($this->url.$this->establishment->id, [
            'name' => ['test'],
            'barangay_code' => ['test'],
            'store_type' => ['test'],
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrorFor('name', 'errors');
    }

    public function test_return_422_validation_error_non_existing_barangay_code_payload(): void
    {
        $response = $this->actingAs($this->auth, 'web')->patchJson($this->url.$this->establishment->id, [
            'barangay_code' => '1234567890',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrorFor('barangay_code', 'errors');
    }

    public function test_return_422_validation_error_non_existing_store_type_payload(): void
    {
        $response = $this->actingAs($this->auth, 'web')->patchJson($this->url.$this->establishment->id, [
            'store_type' => 'non existing store type',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrorFor('store_type', 'errors');
    }

    public function test_return_422_validation_error_unique_establishment(): void
    {
        // create another existing establishment record
        $existing = Establishment::factory()->create();

        // update $this->establishment and use the $existing data
        $response = $this->actingAs($this->auth, 'web')->patchJson($this->url.$this->establishment->id, [
            'name' => $existing->name,
            'barangay_code' => $existing->barangay_code,
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors(['name', 'barangay_code'], 'errors');
    }

    public function test_return_200_ok_when_successfully_updating_establishment(): void
    {
        $barangay = Barangay::factory()->create();
        $storeType = StoreType::factory()->create();

        $data = [
            'name' => 'Lorem Ipsum Supermall',
            'store_type' => $storeType->name,
            'barangay_code' => $barangay->code,
        ];

        $response = $this->actingAs($this->auth, 'web')->patchJson($this->url.$this->establishment->id, $data);

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->has(
                    'data',
                    fn ($json) => $json
                        ->where('name', $data['name'])
                        ->has('store_type', fn (AssertableJson $json) => $json->where('name', $storeType->name)->etc())
                        ->has('address', fn (AssertableJson $json) => $json->where('barangay', $barangay->name)->etc())
                        ->etc(),
                ),
            );

        $this->assertDatabaseHas('establishments', [
            'name' => $data['name'],
            'store_type_id' => $storeType->id,
            'barangay_code' => $barangay->code,
        ]);
    }

    public function test_return_204_no_content_idempotent_update_when_there_is_no_payload(): void
    {
        $response = $this->actingAs($this->auth, 'web')->patchJson($this->url.$this->establishment->id, []);

        $response->assertNoContent();
    }

    public function test_return_404_not_found_when_updating_establishment_by_unauthorize_user(): void
    {
        $owner = User::factory()->create();
        $establishment = Establishment::factory()->for($owner)->create();

        // $auth is not the owner of the establishment, but still trying to update it.
        $response = $this->actingAs($this->auth, 'web')->patchJson($this->url.$establishment->id, [
            'name' => 'Lorem Ipsum Supermarket',
        ]);

        $response->assertNotFound()->assertJson(['error' => __('common.not_found.establishment')]);

        $this->assertAuthenticated('web');
    }
}
