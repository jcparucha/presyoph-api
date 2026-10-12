<?php

namespace Tests\Feature\V1\Products;

use App\Models\Barangay;
use App\Models\Product;
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

    // private Product $product;

    private $url = '/api/v1/products/';

    protected function setUp(): void
    {
        parent::setUp();

        $this->auth = User::factory()->create();
        // $this->product = Product::factory()->for($this->auth)->create();
    }

    public function test_return_401_when_user_is_not_authenticated(): void
    {
        $response = $this->postJson($this->url, ['name' => 'Lorem Ipsum']);

        $response->assertUnauthorized()->assertJson(['error' => 'Unauthenticated.']);
    }

    // TODO add validation testing

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
