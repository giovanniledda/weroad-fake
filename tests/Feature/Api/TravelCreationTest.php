<?php

namespace Tests\Feature\Api;

use App\Models\Travel;
use Illuminate\Testing\Fluent\AssertableJson;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TravelCreationTest extends TestCase
{
    /**
     * @test
     */
    public function guests_cannot_create_new_travels()
    {
        $travel = Travel::factory()->raw();

        $this->postJson('api/v1/travels', $travel)
                         ->assertStatus(401);
    }

    /**
     * @test
     */
    public function editors_cannot_create_new_travels()
    {
        $travel = Travel::factory()->raw();

        $editor = $this->createEditor();

        Sanctum::actingAs($editor);

        $response = $this->postJson('api/v1/travels', $travel);

        $response->assertStatus(401);
    }

    /**
     * @test
     */
    public function admins_can_create_new_travels()
    {
        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $travel = Travel::factory()->raw();

        $response = $this->postJson('api/v1/travels', $travel)
            ->assertStatus(201);

        $createdTravel = Travel::find(1);

        /*
         *   Example:
         *
               {
                "id": "d408be33-aa6a-4c73-a2c8-58a70ab2ba4d",
                "slug": "jordan-360",
                "name": "Jordan 360°",
                "description": "Jordan 360°: the perfect tour to discover the suggestive Wadi Rum desert, the ancient beauty of Petra, and much more.\n\nVisiting Jordan is one of the most fascinating things that everyone has to do once in their life.You are probably wondering \"Why?\". Well, that's easy: because this country keeps several passions! During our tour in Jordan, you can range from well-preserved archaeological masterpieces to trekkings, from natural wonders excursions to ancient historical sites, from camels trek in the desert to some time to relax.\nDo not forget to float in the Dead Sea and enjoy mineral-rich mud baths, it's one of the most peculiar attractions. It will be a tour like no other: this beautiful country leaves a memorable impression on everyone.",
                "numberOfDays": 8,
                "moods": {
                  "nature": 80,
                  "relax": 20,
                  "history": 90,
                  "culture": 30,
                  "party": 10
                }
         */

        $response
            ->assertJson(fn (AssertableJson $json) => $json->where('id', $createdTravel->uuid)
                ->where('slug', $createdTravel->slug)
                ->where('name', $travel['name'])
                ->where('description', $travel['description'])
                ->where('numberOfDays', $travel['days'])
                ->where('moods', $createdTravel->moods['moods'])
                ->missing('nights')
                ->etc()
            );

        $this->assertDatabaseHas('travels', [
            'name' => $travel['name'],
        ]);
    }

    /**
     * @test
     */
    public function name_field_for_new_travels_is_mandatory()
    {
        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $travel = Travel::factory()->raw();

        $name = $travel['name'];

        unset($travel['name']);

        $this->postJson('api/v1/travels', $travel)
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('name');

        $this->assertDatabaseMissing('travels', [
            'name' => $name,
        ]);
    }

    /**
     * @test
     */
    public function description_field_for_new_travels_is_mandatory()
    {
        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $travel = Travel::factory()->raw();

        $description = $travel['description'];

        unset($travel['description']);

        $this->postJson('api/v1/travels', $travel)
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('description');

        $this->assertDatabaseMissing('travels', [
            'description' => $description,
        ]);
    }

    /**
     * @test
     */
    public function days_field_for_new_travels_is_mandatory()
    {
        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $travel = Travel::factory()->raw();

        $days = $travel['days'];

        unset($travel['days']);

        $this->postJson('api/v1/travels', $travel)
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('days');

        $this->assertDatabaseMissing('travels', [
            'days' => $days,
        ]);
    }
}
