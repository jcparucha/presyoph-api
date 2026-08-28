<?php

namespace Tests\Feature\V1\Brands;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetBrandTest extends TestCase
{
    use RefreshDatabase;

    private string $url = '/api/v1/brands';

    private $dataStructure = ['id', 'name', 'slug'];

    public function test_return_200_ok_get_all_brands(): void
    {
        User::factory()->has(Brand::factory(5))->create();

        $response = $this->getJson($this->url);

        $response
            ->assertOk()
            ->assertJsonStructure(['data' => [$this->dataStructure]])
            ->assertJsonCount(5, 'data');
    }

    public function test_return_200_ok_get_specific_brand(): void
    {
        $user = User::factory()->create();
        $brand = Brand::factory(1)->for($user)->create()->first();

        $response = $this->getJson($this->url.'/'.$brand->slug);

        $response->assertOk()->assertJson([
            'data' => [
                'id' => $brand->id,
                'name' => $brand->name,
                'slug' => $brand->slug,
            ],
        ]);
    }

    public function test_returns_not_found_for_an_unknown_brand(): void
    {
        $response = $this->getJson($this->url.'/non-exiting-slug');

        $response->assertNotFound()->assertJson(['error' => 'Brand not found.']);
    }
}
