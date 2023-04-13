<?php

namespace Tests\Feature\Api;

use App\Models\Travel;
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

        // Authenticate the user and attach the token to the request
        Sanctum::actingAs($editor);

        $response = $this->postJson('api/v1/travels', $travel);

        $response->assertStatus(401);
    }

    /**
     * @test
     * TODO: verificare il json
     */
    public function admins_can_create_new_travels()
    {

        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $travel = Travel::factory()->raw();

        $response = $this->postJson('api/v1/travels', $travel)
            ->assertStatus(201)
//            ->json()
//            ->assertJsonPath('name', $travel['name'])
        ;

//        ray($response->json());

        $this->assertDatabaseHas('travels', [
            'name' => $travel['name']
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
            'name' => $name
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
            'description' => $description
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
            'days' => $days
        ]);
    }

}
