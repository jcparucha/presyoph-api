<?php

namespace Tests\Feature\V1\Brands;

use App\Models\Brand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class UpdateBrandTest extends TestCase
{
    use RefreshDatabase;

    private $url = '/api/v1/brands/';

    public function test_return_401_when_user_is_not_authenticated(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $brand = Brand::factory()->for($user)->create()->first();
        $resourceURI = $this->url.$brand->slug;

        $response = $this->patchJson($resourceURI, ['name' => 'Updated Brand']);

        $response->assertUnauthorized()->assertJson(['error' => 'Unauthenticated.']);
    }

    public function test_return_422_validation_error_payload_required(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $brand = Brand::factory()->for($user)->create()->first();
        $resourceURI = $this->url.$brand->slug;

        $response1 = $this->actingAs($user, 'web')->patchJson($resourceURI, []);
        $response2 = $this->actingAs($user, 'web')->patchJson($resourceURI, ['name' => '']);

        $response1->assertUnprocessable()->assertJsonValidationErrors('name', 'errors');
        $response2->assertUnprocessable()->assertJsonValidationErrors('name', 'errors');
    }

    public function test_return_422_validation_error_payload_min_max_characters(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $brand = Brand::factory()->for($user)->create()->first();
        $resourceURI = $this->url.$brand->slug;

        $response1 = $this->actingAs($user, 'web')->patchJson($resourceURI, ['name' => 'as']);
        $response2 = $this->actingAs($user, 'web')->patchJson($resourceURI, [
            'name' => 'qwertyuiopasdfghjkl qwertyuiopasdfghjkl zxcvbnmzxcvbnm',
        ]);

        $response1->assertUnprocessable()->assertJsonValidationErrors('name', 'errors');
        $response2->assertUnprocessable()->assertJsonValidationErrors('name', 'errors');
    }

    public function test_return_422_validation_error_payload_unsupported_characters(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $brand = Brand::factory()->for($user)->create()->first();
        $resourceURI = $this->url.$brand->slug;

        $response = $this->actingAs($user, 'web')->patchJson($resourceURI, ['name' => 'Brand 1 (***)']);

        $response->assertUnprocessable()->assertJsonValidationErrors('name', 'errors');
    }

    public function test_return_422_validation_error_payload_should_be_unique(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $brands = Brand::factory(2)->for($user)->create();
        $firstBrand = $brands->first();
        $lastBrand = $brands->last();

        $resourceURI = $this->url.$firstBrand->slug;

        // try to update the $firstBrand using the name of the $lastBrand
        $response = $this->actingAs($user, 'web')->patchJson($resourceURI, ['name' => $lastBrand->name]);

        $response->assertUnprocessable()->assertJsonValidationErrors('name', 'errors');
    }

    public function test_return_404_not_found_when_updating_brand_by_unauthorize_user(): void
    {
        /** @var User $auth */
        $auth = User::factory()->create();
        $owner = User::factory()->create();
        $brand = Brand::factory()->for($owner)->create()->first();
        $resourceURI = $this->url.$brand->slug;

        // $auth is not the owner of the grocery list, but still trying to update it.
        $response = $this->actingAs($auth, 'web')->patchJson($resourceURI, ['name' => 'New Brand']);

        $response->assertNotFound()->assertJson(['error' => __('common.not_found.brand')]);
    }

    public function test_return_200_ok_when_successfully_updating_brand(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $brand = Brand::factory()->for($user)->create()->first();
        $resourceURI = $this->url.$brand->slug;

        $data = ['name' => 'New Brand 1'];

        $response = $this->actingAs($user, 'web')->patchJson($resourceURI, $data);

        $response
            ->assertOk()
            ->assertJson(
                fn (AssertableJson $json) => $json->has(
                    'data',
                    fn ($json) => $json
                        ->where('name', $data['name'])
                        ->whereType('id', 'integer')
                        ->whereType('slug', 'string'),
                ),
            );
    }
}
