<?php

namespace Tests\Feature\Api;

use App\Models\Travel;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TravelUpdateTest extends TestCase
{
    /**
     * @test
     */
    public function guests_cannot_update_travels()
    {
        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;

        $travelNewData = Travel::factory()->raw();

        $this->putJson("api/v1/travels/{$uuid}", $travelNewData)
            ->assertStatus(401);
    }

    /**
     * @test
     */
    public function editors_can_update_travels()
    {
        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;

        $travelNewData = Travel::factory()->raw();

        $editor = $this->createEditor();

        // Authenticate the user and attach the token to the request
        Sanctum::actingAs($editor);

        $response = $this->putJson("api/v1/travels/{$uuid}", $travelNewData)
            ->assertStatus(200);

        $this->assertDatabaseHas('travels', [
            'uuid' => $uuid,
            'name' => $travelNewData['name'],
        ]);
    }

    /**
     * @test
     */
    public function admins_can_update_travels()
    {
        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;

        $travelNewData = Travel::factory()->raw();

        $admin = $this->createAdmin();

        // Authenticate the user and attach the token to the request
        Sanctum::actingAs($admin);

        $response = $this->putJson("api/v1/travels/{$uuid}", $travelNewData)
            ->assertStatus(200);

        $this->assertDatabaseHas('travels', [
            'uuid' => $uuid,
            'name' => $travelNewData['name'],
        ]);
    }

    /**
     * @test
     */
    public function name_field_for_travels_is_mandatory()
    {
        $editor = $this->createEditor();

        Sanctum::actingAs($editor);

        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;

        $oldName = $travel->name;

        $travelNewData = Travel::factory()->raw();

        $newName = $travelNewData['name'];

        unset($travelNewData['name']);

        $response = $this->patchJson("api/v1/travels/{$uuid}", $travelNewData)
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('name');

        $this->assertDatabaseMissing('travels', [
            'uuid' => $uuid,
            'name' => $newName,
        ]);

        $this->assertDatabaseHas('travels', [
            'uuid' => $uuid,
            'name' => $oldName,
        ]);
    }

    /**
     * @test
     */
    public function description_field_for_travels_is_mandatory()
    {
        $editor = $this->createEditor();

        Sanctum::actingAs($editor);

        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;

        $oldDescription = $travel->description;

        $travelNewData = Travel::factory()->raw();

        $newDescription = $travelNewData['description'];

        unset($travelNewData['description']);

        $response = $this->patchJson("api/v1/travels/{$uuid}", $travelNewData)
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('description');

        $this->assertDatabaseMissing('travels', [
            'uuid' => $uuid,
            'description' => $newDescription,
        ]);

        $this->assertDatabaseHas('travels', [
            'uuid' => $uuid,
            'description' => $oldDescription,
        ]);
    }

    /**
     * @test
     */
    public function days_field_for_travels_is_mandatory()
    {
        $editor = $this->createEditor();

        Sanctum::actingAs($editor);

        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;

        $oldDays = $travel->days;

        $travelNewData = Travel::factory()->raw();

        $newDays = $travelNewData['days'];

        unset($travelNewData['days']);

        $response = $this->patchJson("api/v1/travels/{$uuid}", $travelNewData)
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('days');

        $this->assertDatabaseMissing('travels', [
            'uuid' => $uuid,
            'days' => $newDays,
        ]);

        $this->assertDatabaseHas('travels', [
            'uuid' => $uuid,
            'days' => $oldDays,
        ]);
    }
}
