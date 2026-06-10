<?php

namespace Tests\Feature\GroceryList;

use App\Models\GroceryList;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class GetGroceryListTest extends TestCase
{
    use RefreshDatabase;

    private $url = '/api/v1/users/:id/groceryLists';

    private $dataStructure = [
        'id',
        'name',
        'slug',
        'description',
        'published',
        'created_at',
        'updated_at',
        'created_by',
    ];

    public function test_return_user_empty_grocery_lists(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson(Str::replace(':id', $user->id, $this->url));

        $response->assertStatus(200)->assertJson(['data' => []]);
    }

    public function test_return_user_all_grocery_lists(): void
    {
        $user = User::factory()
            ->has(
                GroceryList::factory()
                    ->count(4)
                    ->sequence(['is_public' => 0], ['is_public' => 1]),
                'groceryLists',
            )
            ->create();

        $response = $this->getJson(Str::replace(':id', $user->id, $this->url));

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data' => [$this->dataStructure]])
            ->assertJson(fn (AssertableJson $json) => $json->has('data', 4));
    }

    public function test_return_user_unpublished_grocery_lists(): void
    {
        $user = User::factory()
            ->has(
                GroceryList::factory()
                    ->count(4)
                    ->sequence(['is_public' => 0], ['is_public' => 1]),
                'groceryLists',
            )
            ->create();

        $response = $this->getJson(Str::replace(':id', $user->id, $this->url).'?published=false');

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data' => [$this->dataStructure]])
            ->assertJson(
                fn (AssertableJson $json) => $json->has(
                    'data',
                    2,
                    fn (AssertableJson $json) => $json->where('published', false)->etc(),
                ),
            );
    }

    public function test_return_user_published_grocery_lists(): void
    {
        $user = User::factory()
            ->has(
                GroceryList::factory()
                    ->count(4)
                    ->sequence(['is_public' => 0], ['is_public' => 1]),
                'groceryLists',
            )
            ->create();

        $response = $this->getJson(Str::replace(':id', $user->id, $this->url).'?published=true');

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data' => [$this->dataStructure]])
            ->assertJson(
                fn (AssertableJson $json) => $json->has(
                    'data',
                    2,
                    fn (AssertableJson $json) => $json->where('published', true)->etc(),
                ),
            );
    }

    public function test_return_422_error_for_incorrect_published_value(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson(Str::replace(':id', $user->id, $this->url).'?published=yes');

        $response->assertStatus(422)->assertJson(['message' => 'The published field must be true or false.']);
    }

    public function test_return_user_specific_grocery_list(): void
    {
        $user = User::factory()->create();

        $groceryList = GroceryList::factory()
            ->count(2)
            ->sequence(['name' => 'Grocery List'], ['name' => 'Test Grocery List'])
            ->for($user)
            ->create();

        $first = $groceryList->first();

        $response = $this->getJson(Str::replace(':id', $user->id, $this->url).'/'.$first->slug);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data' => $this->dataStructure])
            ->assertJson([
                'data' => [
                    'id' => $first->id,
                    'name' => 'Grocery List',
                    'slug' => $first->slug,
                    'description' => $first->description,
                    'published' => intval($first->is_public),
                    'created_at' => $first->created_at->toISOString(),
                    'updated_at' => $first->updated_at->toISOString(),
                    'created_by' => $user->username,
                ],
            ]);
    }

    public function test_return_user_specific_non_existing_grocery_list(): void
    {
        $user = User::factory()->create();

        $response = $this->getJson(Str::replace(':id', $user->id, $this->url).'/non-existing-slug');

        $response->assertNotFound()->assertJson(['error' => 'GroceryList not found.']);
    }
}
