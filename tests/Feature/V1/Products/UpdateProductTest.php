<?php

namespace Tests\Feature\V1\Products;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateProductTest extends TestCase
{
    use RefreshDatabase;

    private User $auth;

    private Product $product;

    private $url = '/api/v1/products/';

    protected function setUp(): void
    {
        parent::setUp();

        $this->auth = User::factory()->create();
        $this->product = Product::factory()->for($this->auth)->create();
    }

    public function test_return_401_when_user_is_not_authenticated(): void
    {
        $response = $this->patchJson($this->url.$this->product->id, ['name' => 'Lorem Ipsum']);

        $response->assertUnauthorized()->assertJson(['error' => 'Unauthenticated.']);
    }

    public function test_return_404_not_found_on_non_existing_product(): void
    {
        $response = $this->actingAs($this->auth)->patchJson($this->url.'999999', [
            'name' => 'Test Name',
        ]);

        $response->assertNotFound()->assertJson(['error' => __('common.not_found.product')]);
    }

    public function test_return_422_validation_error_payload_required()
    {
        $response = $this->actingAs($this->auth, 'web')->patchJson($this->url.$this->product->id, [
            'name' => '',
            'weight' => '',
            'unit' => '',
            'brand' => '',
            'category' => ['name' => ''],
        ]);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name', 'weight', 'unit', 'brand', 'category.name'], 'errors');
    }

    public function test_return_422_validation_error_payload_unsupported_special_characters(): void
    {
        $data = [
            'invalid' => [
                'name' => 'Test ^_^',
                'brand' => 'Test ^_^',
                'category' => ['name' => 'Test ^_^'],
            ],
            'keys' => ['name', 'brand', 'category.name'],
        ];

        $response = $this->actingAs($this->auth, 'web')->patchJson($this->url.$this->product->id, $data['invalid']);

        $response->assertUnprocessable()->assertJsonValidationErrors($data['keys'], 'errors');
    }

    public function test_return_422_validation_error_payload_min_max_characters()
    {
        $url = $this->url.$this->product->id;

        $data = [
            'min' => [
                'name' => 'as',
                'brand' => 'df',
                'category' => ['name' => 'gh'],
            ],
            'max' => [
                'name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'brand' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'category' => ['name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'],
            ],
            'keys' => ['name', 'brand', 'category.name'],
        ];

        // Assert min characters is 3
        $response1 = $this->actingAs($this->auth, 'web')->patchJson($url, $data['min']);
        // assert max characters is 50
        $response2 = $this->actingAs($this->auth, 'web')->patchJson($url, $data['max']);

        $response1->assertUnprocessable()->assertJsonValidationErrors($data['keys'], 'errors');
        $response2->assertUnprocessable()->assertJsonValidationErrors($data['keys'], 'errors');
    }

    public function test_return_422_validation_error_payload_weight_min_max_value()
    {
        // Assert min is 1
        $response1 = $this->actingAs($this->auth, 'web')->patchJson($this->url.$this->product->id, [
            'weight' => 0,
        ]);
        // assert max is 10000
        $response2 = $this->actingAs($this->auth, 'web')->patchJson($this->url.$this->product->id, [
            'weight' => 100001,
        ]);

        $response1->assertUnprocessable()->assertJsonValidationErrorFor('weight', 'errors');
        $response2->assertUnprocessable()->assertJsonValidationErrorFor('weight', 'errors');
    }

    public function test_return_422_validation_error_non_existing_unit_payload()
    {
        $response = $this->actingAs($this->auth, 'web')->patchJson($this->url.$this->product->id, [
            'unit' => 'unit',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrorFor('unit', 'errors');
    }

    public function test_return_422_validation_unique_product(): void
    {
        // create new product
        $newProduct = Product::factory()
            ->for($this->auth)
            ->create(['name' => 'New Product']);

        // replace the values of $newProduct with an existing one
        $data = [
            'name' => $this->product->name,
            'weight' => $this->product->weight,
            'unit' => $this->product->unit->abbreviation,
            'brand' => $this->product->brand->name,
        ];

        $response = $this->actingAs($this->auth, 'web')->patchJson($this->url.$newProduct->id, $data);

        $response->assertUnprocessable()->assertJsonValidationErrorFor('system', 'errors');

        // assert that the original $newProduct still persists in the DB
        $this->assertDatabaseHas('products', ['name' => 'New Product']);
    }

    public function test_return_200_ok_when_successfully_updating_product(): void
    {
        $data = [
            'name' => 'Newest Product',
            'weight' => 50,
            'brand' => 'Brand',
        ];

        $response = $this->actingAs($this->auth, 'web')->patchJson($this->url.$this->product->id, $data);

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->has(
                    'data',
                    fn ($json) => $json->where('name', $data['name'])->where('weight', $data['weight'])->etc(),
                ),
            );

        // will automatically created if it does not exists
        $this->assertDatabaseHas('brands', [
            'name' => 'Brand',
        ]);

        $this->assertDatabaseHas('products', [
            'name' => $data['name'],
            'weight' => $data['weight'],
        ]);
    }

    public function test_return_204_no_content_idempotent_update_when_there_is_no_payload(): void
    {
        $response = $this->actingAs($this->auth, 'web')->patchJson($this->url.$this->product->id, []);

        $response->assertNoContent();
    }
}
