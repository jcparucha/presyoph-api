<?php

namespace Tests\Feature\V1\Products;

use App\Models\Barangay;
use App\Models\StoreType;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class StoreProductTest extends TestCase
{
    use RefreshDatabase;

    private User $auth;

    private $url = '/api/v1/products/';

    protected function setUp(): void
    {
        parent::setUp();

        $this->auth = User::factory()->create();
    }

    public function test_return_401_when_user_is_not_authenticated(): void
    {
        $response = $this->postJson($this->url, ['name' => 'Lorem Ipsum']);

        $response->assertUnauthorized()->assertJson(['error' => 'Unauthenticated.']);
    }

    public function test_return_422_validation_error_payload_required()
    {
        // empty payload
        $response = $this->actingAs($this->auth, 'web')->postJson($this->url, []);

        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(
                [
                    'name',
                    'weight',
                    'unit',
                    'brand',
                    'price',
                    'category.name',
                    'establishment.name',
                    'establishment.barangay_code',
                    'establishment.store_type',
                ],
                'errors',
            );
    }

    public function test_return_422_validation_error_payload_unsupported_special_characters(): void
    {
        $data = [
            'invalid' => [
                'name' => 'Test ^_^',
                'brand' => 'Test ^_^',
                'category' => ['name' => 'Test ^_^', 'description' => '^^^ description ^^^'],
                'establishment' => ['name' => 'Test ^_^'],
                'tags' => ['Test ^_^'],
            ],
            'keys' => ['name', 'brand', 'category.name', 'category.description', 'establishment.name', 'tags.0'],
        ];

        $response = $this->actingAs($this->auth, 'web')->postJson($this->url, $data['invalid']);

        // note that other payload have a 'required' error, but this test only targets a specific payload
        $response->assertUnprocessable()->assertJsonValidationErrors($data['keys'], 'errors');
    }

    public function test_return_422_validation_error_payload_min_max_characters()
    {
        $data = [
            'min' => [
                'name' => 'as',
                'brand' => 'df',
                'establishment' => ['name' => 'gh'],
                'category' => ['name' => 'gh', 'description' => 'as'],
            ],
            'max' => [
                'name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'brand' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'establishment' => ['name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.'],
                'category' => [
                    'name' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                    'description' => fake()->paragraph(260, 270),
                ],
            ],
            'keys' => ['name', 'brand', 'category.name', 'category.description', 'establishment.name'],
        ];

        // Assert min characters is 3
        $response1 = $this->actingAs($this->auth, 'web')->postJson($this->url, $data['min']);
        // assert max characters is 50
        $response2 = $this->actingAs($this->auth, 'web')->postJson($this->url, $data['max']);

        // note that other payload have a 'required' error, but this test only targets a specific payload
        $response1->assertUnprocessable()->assertJsonValidationErrors($data['keys'], 'errors');
        $response2->assertUnprocessable()->assertJsonValidationErrors($data['keys'], 'errors');
    }

    public function test_return_422_validation_error_payload_weight_min_max_value()
    {
        // Assert min is 1
        $response1 = $this->actingAs($this->auth, 'web')->postJson($this->url, [
            'weight' => 0,
        ]);
        // assert max is 10000
        $response2 = $this->actingAs($this->auth, 'web')->postJson($this->url, [
            'weight' => 100001,
        ]);

        // note that other payload have a 'required' error, but this test only targets a specific payload
        $response1->assertUnprocessable()->assertJsonValidationErrorFor('weight', 'errors');
        $response2->assertUnprocessable()->assertJsonValidationErrorFor('weight', 'errors');
    }

    public function test_return_422_validation_error_payload_price_value()
    {
        // Assert min is 0.01
        $response1 = $this->actingAs($this->auth, 'web')->postJson($this->url, [
            'price' => 0.00123456,
        ]);
        // assert max is 999999.99
        $response2 = $this->actingAs($this->auth, 'web')->postJson($this->url, [
            'price' => 1000000,
        ]);

        // note that other payload have a 'required' error, but this test only targets a specific payload
        $response1->assertUnprocessable()->assertJsonValidationErrorFor('price', 'errors');
        $response2->assertUnprocessable()->assertJsonValidationErrorFor('price', 'errors');
    }

    public function test_return_422_validation_error_payload_tags_value()
    {
        // tags should be an array
        $response1 = $this->actingAs($this->auth, 'web')->postJson($this->url, [
            'tags' => "I'm a string",
        ]);

        // tag items is following the name rule: min of 3, max of 25
        $response2 = $this->actingAs($this->auth, 'web')->postJson($this->url, [
            'tags' => ['as', 'asdfg qwert zxcvb tyuio ghjkl'],
        ]);

        // tag items should be distinct
        $response3 = $this->actingAs($this->auth, 'web')->postJson($this->url, [
            'tags' => ['food', 'food'],
        ]);

        // note that other payload have a 'required' error, but this test only targets a specific payload
        $response1->assertUnprocessable()->assertJsonValidationErrorFor('tags', 'errors');
        $response2->assertUnprocessable()->assertJsonValidationErrors(['tags.0', 'tags.1'], 'errors');
        $response3->assertUnprocessable()->assertJsonValidationErrors(['tags.0', 'tags.1'], 'errors');
    }

    public function test_return_422_validation_error_non_existing_payload()
    {
        $response = $this->actingAs($this->auth, 'web')->postJson($this->url, [
            'unit' => 'unit',
            'establishment' => ['barangay_code' => '100010101', 'store_type' => 'fake store'],
        ]);

        // note that other payload have a 'required' error, but this test only targets a specific payload
        $response
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['unit', 'establishment.barangay_code', 'establishment.store_type'], 'errors');
    }

    public function test_return_201_created_when_successfully_creating_product(): void
    {
        $data = $this->productPayload();

        $response = $this->actingAs($this->auth, 'web')->postJson($this->url, $data);

        $response
            ->assertCreated()
            ->assertHeaderContains('Location', 'http://localhost'.$this->url)
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('links')
                    ->has(
                        'data',
                        fn (AssertableJson $json) => $json
                            ->where('name', $data['name'])
                            ->where('weight', $data['weight'])
                            ->where('added_by', $this->auth->username)
                            ->has(
                                'unit',
                                fn (AssertableJson $json) => $json->where('abbreviation', $data['unit'])->etc(),
                            )
                            ->has('brand', fn (AssertableJson $json) => $json->where('name', $data['brand'])->etc())
                            ->has(
                                'category',
                                fn (AssertableJson $json) => $json->where('name', $data['category']['name'])->etc(),
                            )
                            ->has('tags', 1, fn (AssertableJson $json) => $json->where('name', $data['tags'][0])->etc())
                            ->has(
                                'latest_price',
                                1,
                                fn (AssertableJson $json) => $json
                                    ->where('price', $data['price'])
                                    ->has(
                                        'establishment',
                                        fn (AssertableJson $json) => $json
                                            ->where('name', $data['establishment']['name'])
                                            ->etc(),
                                    )
                                    ->etc(),
                            )
                            ->etc(),
                    ),
            );

        $this->assertDatabaseHas('products', [
            'name' => $data['name'],
            'weight' => $data['weight'],
            'added_by' => $this->auth->id,
        ]);

        $this->assertDatabaseHas('brands', ['name' => $data['brand']]);
        $this->assertDatabaseHas('categories', ['name' => $data['category']['name']]);
        $this->assertDatabaseHas('tags', ['name' => $data['tags'][0], 'added_by' => $this->auth->id]);
        $this->assertDatabaseHas('product_prices', [
            'product_id' => $response->json('data.id'),
            'price' => $data['price'],
        ]);
    }

    public function test_return_200_ok_on_idempotent_post(): void
    {
        $data = $this->productPayload();

        $response1 = $this->actingAs($this->auth, 'web')->postJson($this->url, $data);
        $response2 = $this->actingAs($this->auth, 'web')->postJson($this->url, $data);

        $response1
            ->assertCreated()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('links')
                    ->has(
                        'data',
                        fn (AssertableJson $json) => $json
                            ->where('name', $data['name'])
                            ->where('weight', $data['weight'])
                            ->etc(),
                    ),
            );

        $response2
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json
                    ->has('links')
                    ->has(
                        'data',
                        fn (AssertableJson $json) => $json
                            ->where('name', $data['name'])
                            ->where('weight', $data['weight'])
                            ->etc(),
                    ),
            );

        $this->assertSame($response1->json('data.id'), $response2->json('data.id'));
        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseCount('product_prices', 1);
        $this->assertDatabaseCount('tags', 1);
        $this->assertDatabaseHas('products', [
            'name' => $data['name'],
            'weight' => $data['weight'],
            'added_by' => $this->auth->id,
        ]);
    }

    private function productPayload(): array
    {
        $unit = Unit::factory()->create();
        $barangay = Barangay::factory()->create();
        $storeType = StoreType::factory()->create();

        return [
            'name' => 'Sample Product',
            'weight' => 250,
            'unit' => $unit->abbreviation,
            'brand' => 'Sample Brand',
            'category' => [
                'name' => 'Sample Category',
                'description' => 'Sample category description',
            ],
            'establishment' => [
                'name' => 'Sample Store',
                'barangay_code' => $barangay->code,
                'store_type' => $storeType->name,
            ],
            'price' => 12.5,
            'tags' => ['Sample Tag'],
        ];
    }
}
