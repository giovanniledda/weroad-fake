<?php

namespace Tests\Feature\Api;

use App\Models\Tour;
use App\Models\Travel;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use function now;

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
            'travelId' => null
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
            'travelId' => null
        ]);

        $this->postJson("api/v1/travels/{$uuid}/tour", $newTour)
            ->assertStatus(401);
    }

    /**
     * @test
     * TODO: verificare il json
     */
    public function admins_can_create_new_tour_for_travels()
    {

        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;

        $newTour = Tour::factory()->raw([
            'travelId' => null,
            'startingDate' => now()->addMonth(),
            'endingDate' => now()->addMonths(7),
        ]);

        $response = $this->postJson("api/v1/travels/{$uuid}/tour", $newTour)
            ->assertStatus(201);

//        ray($response->json());

        $this->assertDatabaseHas('tours', [
            'name' => $newTour['name'],
            'travelId' => $travel->id
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
            'travelId' => null
        ]);

        $tourOriginalName = $newTour['name'];

        unset($newTour['name']);

        $this->postJson("api/v1/travels/{$uuid}/tour", $newTour)
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('name');

        $this->assertDatabaseMissing('tours', [
            'name' => $tourOriginalName,
            'travelId' => $travel->id
        ]);
    }


    /**
     * @test
     */
    public function date_fields_for_new_tours_are_mandatory()
    {

        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;

        $newTour = Tour::factory()->raw([
            'travelId' => null
        ]);

        $tourOriginalStartingDate = $newTour['startingDate'];

        $tourOriginalEndingDate = $newTour['endingDate'];

        // missing
        unset($newTour['startingDate']);

        // wrong date format
        $newTour['endingDate'] = 'x91wkm129kwx21kx2190';

        $this->postJson("api/v1/travels/{$uuid}/tour", $newTour)
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('startingDate')
            ->assertJsonValidationErrorFor('endingDate');

        $this->assertDatabaseMissing('tours', [
            'startingDate' => $tourOriginalStartingDate,
            'endingDate' => $tourOriginalEndingDate,
            'travelId' => $travel->id
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
            'startingDate' => now()->subDays(7)
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
            'travelId' => $travel->id
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
            'endingDate' => now()
        ]);

        $tourOriginalStartingDate = $newTour['startingDate'];

        $tourOriginalEndingDate = $newTour['endingDate'];

        $this->postJson("api/v1/travels/{$uuid}/tour", $newTour)
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('endingDate');

        $this->assertDatabaseMissing('tours', [
            'startingDate' => $tourOriginalStartingDate,
            'endingDate' => $tourOriginalEndingDate,
            'travelId' => $travel->id
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
            'travelId' => null
        ]);

        $tourOriginalPrice = $newTour['price'];

        unset($newTour['price']);

        $this->postJson("api/v1/travels/{$uuid}/tour", $newTour)
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('price');

        $this->assertDatabaseMissing('tours', [
            'price' => $tourOriginalPrice,
            'travelId' => $travel->id
        ]);
    }

}
