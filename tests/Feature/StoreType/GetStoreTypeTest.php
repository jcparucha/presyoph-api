<?php

namespace Tests\Feature\StoreType;

use App\Models\StoreType;
use Database\Seeders\StoreTypeSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetStoreTypeTest extends TestCase
{
    use RefreshDatabase;

    private $url = '/api/v1/store_types';

    public function test_return_empty_list_when_no_store_types_records(): void
    {
        $response = $this->getJson($this->url);

        $response->assertStatus(200)->assertJson(['data' => []]);
    }

    public function test_return_seeded_store_types(): void
    {
        // Arrange
        $this->seed(StoreTypeSeeder::class);

        // Act by sending Http request
        $response = $this->getJson('/api/v1/store_types');

        // Assert
        $response->assertStatus(200)->assertJsonStructure([
            'data' => [['id', 'name', 'slug']],
        ]);
    }

    public function test_return_specific_seeded_store_type_by_id(): void
    {
        // Arrange
        $this->seed(StoreTypeSeeder::class);

        $storeType = StoreType::first();

        // Act
        $response = $this->getJson($this->url.'/'.$storeType->id);

        // Assert
        $response->assertStatus(200)->assertJson([
            'data' => [
                'id' => $storeType->id,
                'name' => $storeType->name,
                'slug' => $storeType->slug,
            ],
        ]);
    }
}
