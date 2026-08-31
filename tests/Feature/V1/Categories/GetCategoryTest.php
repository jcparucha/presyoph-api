<?php

namespace Tests\Feature\V1\Categories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetCategoryTest extends TestCase
{
    use RefreshDatabase;

    private string $url = '/api/v1/categories';

    private $dataStructure = ['id', 'name', 'slug', 'description'];

    public function test_return_200_ok_get_all_categories(): void
    {
        User::factory()->has(Category::factory(5))->create();

        $response = $this->getJson($this->url);

        $response
            ->assertOk()
            ->assertJsonStructure(['data' => [$this->dataStructure]])
            ->assertJsonCount(5, 'data');
    }

    public function test_return_200_ok_get_specific_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory(1)->for($user)->create()->first();

        $response = $this->getJson($this->url.'/'.$category->slug);

        $response->assertOk()->assertJson([
            'data' => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'description' => $category->description,
            ],
        ]);
    }

    public function test_returns_not_found_for_an_unknown_category(): void
    {
        $response = $this->getJson($this->url.'/non-exiting-slug');

        $response->assertNotFound()->assertJson(['error' => 'Category not found.']);
    }
}
