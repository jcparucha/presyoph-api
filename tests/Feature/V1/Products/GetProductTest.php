<?php

namespace Tests\Feature\V1\Products;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetProductTest extends TestCase
{
    use RefreshDatabase;

    private $url = '/api/v1/products';

    private array $dataStructure = [
        'id',
        'name',
        'weight',
        'added_by',
        'unit' => ['id', 'name', 'abbreviation'],
        'brand' => ['id', 'name', 'slug'],
        'category' => ['id', 'name', 'slug', 'description'],
        'tags' => [
            '*' => ['id', 'name', 'slug'],
        ],
        'latest_price' => [
            '*' => [
                'id',
                'price',
                'recorded_at',
                'added_by',
                'establishment' => [
                    'id',
                    'name',
                    'store_type' => ['id', 'name', 'slug'],
                    'address' => ['barangay', 'munCity', 'province', 'region'],
                ],
            ],
        ],
    ];

    public function test_return_200_ok_get_all_products(): void
    {
        Product::factory(5)->create();

        $response = $this->getJson($this->url);

        $response
            ->assertOk()
            ->assertJsonStructure(['data' => ['*' => $this->dataStructure]])
            ->assertJsonCount(5, 'data');
    }

    public function test_return_200_ok_get_specific_products(): void
    {
        $product = Product::factory()->create()->first();

        $response = $this->getJson($this->url.'/'.$product->id);

        $response
            ->assertOk()
            ->assertJsonStructure(['data' => $this->dataStructure])
            ->assertJson([
                'data' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    // ...
                ],
            ]);
    }

    public function test_returns_not_found_for_an_unknown_products(): void
    {
        $response = $this->getJson($this->url.'/non-exiting-slug');

        $response->assertNotFound()->assertJson(['error' => 'Product not found.']);
    }
}
