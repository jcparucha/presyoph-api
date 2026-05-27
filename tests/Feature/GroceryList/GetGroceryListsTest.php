<?php

namespace Tests\Feature\GroceryList;

use App\Models\GroceryList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetGroceryListsTest extends TestCase
{
    use RefreshDatabase;

    private $url = '/api/v1/users/:id/grocery_lists';

    private $dataStructure = [
        ['id', 'name', 'slug', 'description', 'published', 'created_at', 'updated_at', 'created_by'],
    ];

    public function test_return_user_empty_grocery_lists(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson(Str::replace(':id', $user->id, $this->url));

        $response->assertStatus(200)->assertJson(['data' => []]);
    }

    public function test_return_user_unpublished_grocery_lists(): void
    {
        $user = User::factory()
            ->has(GroceryList::factory()->count(3), 'groceryLists')
            ->create();

        $response = $this->getJson(Str::replace(':id', $user->id, $this->url));

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data' => $this->dataStructure])
            ->assertJson(function (AssertableJson $json) {
                // check that data should return 3 unpublished grocery list
                $json->has('data', 3, fn (AssertableJson $json) => $json->where('published', 0)->etc());
            });
    }

    public function test_return_user_published_grocery_lists(): void
    {
        $user = User::factory()
            ->has(GroceryList::factory()->published()->count(3), 'groceryLists')
            ->create();

        $response = $this->getJson(Str::replace(':id', $user->id, $this->url));

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data' => $this->dataStructure])
            ->assertJson(function (AssertableJson $json) {
                // check that data should return 3 published grocery list
                $json->has('data', 3, fn (AssertableJson $json) => $json->where('published', 1)->etc());
            });
    }

    // TODO create test for specific grocery list
}
