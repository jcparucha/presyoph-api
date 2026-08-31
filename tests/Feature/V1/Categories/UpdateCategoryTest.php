<?php

namespace Tests\Feature\V1\Categories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateCategoryTest extends TestCase
{
    use RefreshDatabase;

    private $url = '/api/v1/categories/';

    private $fields = ['name', 'description'];

    public function test_return_401_when_user_is_not_authenticated(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create()->first();
        $resourceURI = $this->url.$category->slug;

        $response = $this->patchJson($resourceURI, ['name' => 'Updated Category']);

        $response->assertUnauthorized()->assertJson(['error' => 'Unauthenticated.']);
    }

    public function test_return_422_validation_error_payload_min_max_characters(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create()->first();
        $resourceURI = $this->url.$category->slug;

        // name and description are optional
        $response1 = $this->actingAs($user, 'web')->patchJson($resourceURI, ['name' => 'as']);
        $response2 = $this->actingAs($user, 'web')->patchJson($resourceURI, [
            'description' => fake()->paragraph(260, 270),
        ]);

        $response1->assertUnprocessable()->assertJsonValidationErrors('name', 'errors');
        // only description should an error
        $response2->assertUnprocessable()->assertJsonValidationErrors('description', 'errors');
    }

    public function test_return_422_validation_error_payload_unsupported_characters(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create()->first();
        $resourceURI = $this->url.$category->slug;

        $response = $this->actingAs($user, 'web')->patchJson($resourceURI, [
            'name' => 'Category 1 (***)',
            'description' => 'Some long text with invalid character *^* ~_~',
        ]);

        $response->assertUnprocessable()->assertJsonValidationErrors($this->fields, 'errors');
    }

    public function test_return_422_validation_error_payload_should_be_unique(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $categories = Category::factory(2)->for($user)->create();
        $firstCategory = $categories->first();
        $lastCategory = $categories->last();

        $resourceURI = $this->url.$firstCategory->slug;

        // try to update the $firstCategory using the name of the $lastCategory
        $response = $this->actingAs($user, 'web')->patchJson($resourceURI, ['name' => $lastCategory->name]);

        $response->assertUnprocessable()->assertJsonValidationErrors('name', 'errors');
    }

    public function test_return_404_not_found_when_updating_category_by_unauthorize_user(): void
    {
        /** @var User $auth */
        $auth = User::factory()->create();
        $owner = User::factory()->create();
        $category = Category::factory()->for($owner)->create()->first();
        $resourceURI = $this->url.$category->slug;

        // $auth is not the owner of the category, but still trying to update it.
        $response = $this->actingAs($auth, 'web')->patchJson($resourceURI, ['name' => 'New Category']);

        $response->assertNotFound()->assertJson(['error' => __('common.not_found.category')]);
    }

    public function test_return_200_ok_when_successfully_updating_category(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $category = Category::factory()->for($user)->create()->first();
        $resourceURI = $this->url.$category->slug;

        $data = ['name' => 'New Category 1', 'description' => 'New Description 1'];

        $response = $this->actingAs($user, 'web')->patchJson($resourceURI, $data);

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->has(
                    'data',
                    fn ($json) => $json
                        ->where('name', $data['name'])
                        ->where('description', $data['description'])
                        ->whereType('id', 'integer')
                        ->whereType('slug', 'string'),
                ),
            );

        $this->assertDatabaseHas('categories', $data);
    }
}
