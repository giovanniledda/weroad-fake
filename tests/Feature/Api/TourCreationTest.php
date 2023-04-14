<?php

namespace Tests\Feature\Api;

use App\Models\Tour;
use App\Models\Travel;
use Illuminate\Support\Carbon;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use function fake;
use function now;
use Tests\TestCase;

class TourCreationTest extends TestCase
{
    /**
     * @test
     */
    public function guests_cannot_create_new_tour_for_travels()
    {
        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;

        $newTour = Tour::factory()->raw([
            'travelId' => null,
        ]);

        $this->postJson("api/v1/travels/{$uuid}/tour", $newTour)
            ->assertStatus(401);
    }

    /**
     * @test
     */
    public function editors_cannot_create_new_tour_for_travels()
    {
        $editor = $this->createEditor();

        Sanctum::actingAs($editor);

        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;

        $newTour = Tour::factory()->raw([
            'travelId' => null,
        ]);

        $this->postJson("api/v1/travels/{$uuid}/tour", $newTour)
            ->assertStatus(401);
    }

    /**
     * @test
     */
    public function admins_can_create_new_tour_for_travels()
    {
        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;

        $newTour = Tour::factory()->raw([
            'travelId' => null,
            'startingDate' => now()->addMonth()->format('Y-m-d'),
            'endingDate' => now()->addMonths(7)->format('Y-m-d'),
        ]);

        $response = $this->postJson("api/v1/travels/{$uuid}/tour", $newTour)
            ->assertStatus(201);

        $createdTour = Tour::find(1);

        /**
         * Example:
         *   {
                "id": "2a0edc99-c9fe-4206-8da5-413586667a21",
                "travelId": "d408be33-aa6a-4c73-a2c8-58a70ab2ba4d",
                "name": "ITJOR20211101",
                "startingDate": "2021-11-01",
                "endingDate": "2021-11-09",
                "price": 199900
                },
         */
        $response
            ->assertJson(fn (AssertableJson $json) => $json->where('id', $createdTour->uuid)
                ->where('travelId', $travel->uuid)
                ->where('name', $newTour['name'])
                ->where('startingDate', $newTour['startingDate'])
                ->where('endingDate', $newTour['endingDate'])
                ->where('price', $newTour['price'])
                ->etc()
            );

        $this->assertDatabaseHas('tours', [
            'name' => $newTour['name'],
            'travelId' => $travel->id,
        ]);
    }

    /**
     * @test
     */
    public function name_field_for_new_tours_is_mandatory()
    {
        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;

        $newTour = Tour::factory()->raw([
            'travelId' => null,
        ]);

        $tourOriginalName = $newTour['name'];

        unset($newTour['name']);

        $this->postJson("api/v1/travels/{$uuid}/tour", $newTour)
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('name');

        $this->assertDatabaseMissing('tours', [
            'name' => $tourOriginalName,
            'travelId' => $travel->id,
        ]);
    }

    /**
     * @test
     */
    public function starting_date_field_for_new_tours_is_a_mandatory_valid_date()
    {
        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;

        $newTour = Tour::factory()->raw([
            'travelId' => null,
        ]);

        $tourOriginalStartingDate = $newTour['startingDate'];

        $tourOriginalEndingDate = $newTour['endingDate'];

        // missing
        unset($newTour['startingDate']);

        $this->postJson("api/v1/travels/{$uuid}/tour", $newTour)
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('startingDate');

        // wrong date format
        $newTour['startingDate'] = 'x91wkm129kwx21kx2190';

        $this->postJson("api/v1/travels/{$uuid}/tour", $newTour)
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('startingDate');

        $this->assertDatabaseMissing('tours', [
            'startingDate' => $tourOriginalStartingDate,
            'endingDate' => $tourOriginalEndingDate,
            'travelId' => $travel->id,
        ]);
    }

    /**
     * @test
     */
    public function if_ending_date_field_for_new_tours_is_missing_then_travel_days_will_be_considered()
    {
        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;

        $newTour = Tour::factory()->raw([
            'travelId' => null,
        ]);

        $tourOriginalStartingDate = $newTour['startingDate'];

        $tourOriginalEndingDate = $newTour['endingDate'];

        $tourNewEndingDate = Carbon::createFromFormat('Y-m-d', $tourOriginalStartingDate)->addDays($travel->days)->format('Y-m-d');

        // missing
        unset($newTour['endingDate']);

        $this->postJson("api/v1/travels/{$uuid}/tour", $newTour)
            ->assertStatus(201)
            ->assertJsonMissingValidationErrors('startingDate')
            ->assertJsonMissingValidationErrors('endingDate');

        $this->assertDatabaseHas('tours', [
            'id' => 1,
            'startingDate' => $tourOriginalStartingDate,
            'endingDate' => $tourNewEndingDate,
            'travelId' => $travel->id,
        ]);

        // wrong date format
        $newTour['endingDate'] = 'x91wkm129kwx21kx2190';

        $this->postJson("api/v1/travels/{$uuid}/tour", $newTour)
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('endingDate');

        // forcing endingDate
        $newTour['name'] = Tour::factory()->raw()['name'];

        $newTour['endingDate'] = $tourOriginalEndingDate;

        $this->postJson("api/v1/travels/{$uuid}/tour", $newTour)
            ->assertStatus(201)
            ->assertJsonMissingValidationErrors('startingDate')
            ->assertJsonMissingValidationErrors('endingDate');

        $this->assertDatabaseHas('tours', [
            'id' => 2,
            'startingDate' => $tourOriginalStartingDate,
            'endingDate' => $tourOriginalEndingDate,
            'travelId' => $travel->id,
        ]);
    }

    /**
     * @test
     */
    public function starting_date_field_for_new_tours_must_be_in_the_future()
    {
        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;

        $newTour = Tour::factory()->raw([
            'travelId' => null,
            'startingDate' => now()->subDays(7),
        ]);

        $tourOriginalStartingDate = $newTour['startingDate'];

        // wrong date format
        $newTour['endingDate'] = now()->subDays(3);

        $tourOriginalEndingDate = $newTour['endingDate'];

        $this->postJson("api/v1/travels/{$uuid}/tour", $newTour)
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('startingDate');

        $this->assertDatabaseMissing('tours', [
            'startingDate' => $tourOriginalStartingDate,
            'endingDate' => $tourOriginalEndingDate,
            'travelId' => $travel->id,
        ]);
    }

    /**
     * @test
     */
    public function ending_date_field_for_new_tours_must_be_after_starting_date()
    {
        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;

        $newTour = Tour::factory()->raw([
            'travelId' => null,
            'startingDate' => now()->addMonths(7),
            'endingDate' => now(),
        ]);

        $tourOriginalStartingDate = $newTour['startingDate'];

        $tourOriginalEndingDate = $newTour['endingDate'];

        $this->postJson("api/v1/travels/{$uuid}/tour", $newTour)
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('endingDate');

        $this->assertDatabaseMissing('tours', [
            'startingDate' => $tourOriginalStartingDate,
            'endingDate' => $tourOriginalEndingDate,
            'travelId' => $travel->id,
        ]);
    }

    /**
     * @test
     */
    public function price_field_for_new_tours_is_mandatory()
    {
        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;

        $newTour = Tour::factory()->raw([
            'travelId' => null,
        ]);

        $tourOriginalPrice = $newTour['price'];

        unset($newTour['price']);

        $this->postJson("api/v1/travels/{$uuid}/tour", $newTour)
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('price');

        $this->assertDatabaseMissing('tours', [
            'price' => $tourOriginalPrice,
            'travelId' => $travel->id,
        ]);
    }


    /**
     * @test
     */
    public function price_field_for_new_tours_must_be_greater_than_zero()
    {
        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;

        $newTour = Tour::factory()->raw([
            'travelId' => null,
            'price' => 0,
        ]);

        $this->postJson("api/v1/travels/{$uuid}/tour", $newTour)
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('price');

        $this->assertDatabaseMissing('tours', [
            'price' => 0,
            'travelId' => $travel->id,
        ]);
    }
}
