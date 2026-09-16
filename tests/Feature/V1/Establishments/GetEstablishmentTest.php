<?php

namespace Tests\Feature\V1\Establishments;

use App\Models\Barangay;
use App\Models\Establishment;
use App\Models\StoreType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetEstablishmentTest extends TestCase
{
    use RefreshDatabase;

    private string $url = '/api/v1/establishments';

    private $dataStructure = ['id', 'name', 'store_type', 'address'];

    public function test_return_200_ok_get_all_establishments(): void
    {
        Establishment::factory(5)->create();

        $response = $this->getJson($this->url);

        $response
            ->assertOk()
            ->assertJsonStructure(['data' => ['*' => $this->dataStructure]])
            ->assertJsonCount(5, 'data');
    }

    public function test_return_200_ok_get_specific_establishments(): void
    {
        $establishment = Establishment::factory()->create()->first();

        $response = $this->getJson($this->url.'/'.$establishment->id);

        $response
            ->assertOk()
            ->assertJsonStructure(['data' => $this->dataStructure])
            ->assertJson([
                'data' => [
                    'id' => $establishment->id,
                    'name' => $establishment->name,
                    // ...
                ],
            ]);
    }

    public function test_return_200_ok_get_all_establishments_filtered_by_store_type(): void
    {
        $filter = StoreType::factory(2)->create();
        $first = $filter->first();
        $last = $filter->last();

        Establishment::factory(10)
            ->sequence(['store_type_id' => $first->id], ['store_type_id' => $last->id])
            ->create();

        $response1 = $this->getJson($this->url."?store_type={$first->name}");
        $response2 = $this->getJson($this->url."?store_type={$last->name}");

        $response1
            ->assertOk()
            ->assertJsonStructure(['data' => [$this->dataStructure]])
            ->assertJsonCount(5, 'data');

        $response2
            ->assertOk()
            ->assertJsonStructure(['data' => [$this->dataStructure]])
            ->assertJsonCount(5, 'data');
    }

    public function test_return_200_ok_get_all_establishments_filtered_by_barangay(): void
    {
        $filter = Barangay::factory(2)->create();
        $first = $filter->first();
        $last = $filter->last();

        Establishment::factory(10)
            ->sequence(['barangay_code' => $first->code], ['barangay_code' => $last->code])
            ->create();

        $response1 = $this->getJson($this->url."?barangay_code={$first->code}");
        $response2 = $this->getJson($this->url."?barangay_code={$last->code}");

        $response1
            ->assertOk()
            ->assertJsonStructure(['data' => [$this->dataStructure]])
            ->assertJsonCount(5, 'data');

        $response2
            ->assertOk()
            ->assertJsonStructure(['data' => [$this->dataStructure]])
            ->assertJsonCount(5, 'data');
    }

    public function test_return_200_ok_get_all_establishments_filtered_by_muncity(): void
    {
        $filter = Barangay::factory(2)->create();
        $first = $filter->first();
        $last = $filter->last();

        Establishment::factory(10)
            ->sequence(['barangay_code' => $first->code], ['barangay_code' => $last->code])
            ->create();

        $response1 = $this->getJson($this->url."?mun_city_code={$first->munCity->code}");
        $response2 = $this->getJson($this->url."?mun_city_code={$last->munCity->code}");

        $response1
            ->assertOk()
            ->assertJsonStructure(['data' => [$this->dataStructure]])
            ->assertJsonCount(5, 'data');

        $response2
            ->assertOk()
            ->assertJsonStructure(['data' => [$this->dataStructure]])
            ->assertJsonCount(5, 'data');
    }

    public function test_return_200_ok_get_all_establishments_filtered_by_province(): void
    {
        $filter = Barangay::factory(2)->create();
        $first = $filter->first();
        $last = $filter->last();

        Establishment::factory(10)
            ->sequence(['barangay_code' => $first->code], ['barangay_code' => $last->code])
            ->create();

        $response1 = $this->getJson($this->url."?province_code={$first->munCity->province->code}");
        $response2 = $this->getJson($this->url."?province_code={$last->munCity->province->code}");

        $response1
            ->assertOk()
            ->assertJsonStructure(['data' => [$this->dataStructure]])
            ->assertJsonCount(5, 'data');

        $response2
            ->assertOk()
            ->assertJsonStructure(['data' => [$this->dataStructure]])
            ->assertJsonCount(5, 'data');
    }

    public function test_return_200_ok_get_all_establishments_filtered_by_region(): void
    {
        $filter = Barangay::factory(2)->create();
        $first = $filter->first();
        $last = $filter->last();

        Establishment::factory(10)
            ->sequence(['barangay_code' => $first->code], ['barangay_code' => $last->code])
            ->create();

        $response1 = $this->getJson($this->url."?region_code={$first->munCity->province->region->code}");
        $response2 = $this->getJson($this->url."?region_code={$last->munCity->province->region->code}");

        $response1
            ->assertOk()
            ->assertJsonStructure(['data' => [$this->dataStructure]])
            ->assertJsonCount(5, 'data');

        $response2
            ->assertOk()
            ->assertJsonStructure(['data' => [$this->dataStructure]])
            ->assertJsonCount(5, 'data');
    }

    // Add test for establishment request validation
    public function test_return_422_validation_error_for_invalid_store_type_filter()
    {
        // non-existing filter
        $response = $this->getJson($this->url.'?store_type=fake-filter');

        $response->assertUnprocessable()->assertJson(['message' => 'The selected store type is invalid.']);
    }

    public function test_return_422_validation_error_for_invalid_barangay_code_filter()
    {
        // non-existing filter
        $response = $this->getJson($this->url.'?barangay_code=0000011111');

        $response->assertUnprocessable()->assertJson(['message' => 'The selected barangay code is invalid.']);
    }

    public function test_return_422_validation_error_for_invalid_mun_city_code_filter()
    {
        // non-existing filter
        $response = $this->getJson($this->url.'?mun_city_code=0000011111');

        $response->assertUnprocessable()->assertJson(['message' => 'The selected mun city code is invalid.']);
    }

    public function test_return_422_validation_error_for_invalid_province_code_filter()
    {
        // non-existing filter
        $response = $this->getJson($this->url.'?province_code=0000011111');

        $response->assertUnprocessable()->assertJson(['message' => 'The selected province code is invalid.']);
    }

    public function test_return_422_validation_error_for_invalid_region_code_filter()
    {
        // non-existing filter
        $response = $this->getJson($this->url.'?region_code=0000011111');

        $response->assertUnprocessable()->assertJson(['message' => 'The selected region code is invalid.']);
    }

    public function test_return_422_validation_error_for_multiple_location_code_filter()
    {
        $barangay1 = Barangay::factory()->create();
        $barangay2 = Barangay::factory()->create();

        // this request for validating multiple location filter that are different from each other
        $response = $this->getJson(
            $this->url."?barangay_code={$barangay1->code}&mun_city_code={$barangay2->munCity->code}",
        );

        $response
            ->assertUnprocessable()
            ->assertJson(['message' => 'Only one geolocation filter is allowed. (and 1 more error)'])
            ->assertJsonValidationErrors(['barangay_code', 'mun_city_code'], 'errors');
    }

    public function test_returns_not_found_for_an_unknown_brand(): void
    {
        $response = $this->getJson($this->url.'/non-exiting-slug');

        $response->assertNotFound()->assertJson(['error' => 'Establishment not found.']);
    }
}
