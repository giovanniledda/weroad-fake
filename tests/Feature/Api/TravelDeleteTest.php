<?php

namespace Tests\Feature\Api;

use App\Models\Travel;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TravelDeleteTest extends TestCase
{
    /**
     * @test
     */
    public function guests_cannot_delete_travels()
    {
        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;

        $this->deleteJson("api/v1/travels/{$uuid}")
            ->assertStatus(401);
    }

    /**
     * @test
     */
    public function editors_cannot_delete_travels()
    {
        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;


        $editor = $this->createEditor();

        Sanctum::actingAs($editor);

        $response = $this->delete("api/v1/travels/{$uuid}")
            ->assertStatus(401);

    }

    /**
     * @test
     */
    public function admins_can_delete_travels()
    {
        $travel = Travel::factory()->create();

        $uuid = $travel->uuid;

        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $this->assertDatabaseHas('travels', [
            'uuid' => $uuid,
        ]);

        $this->deleteJson("api/v1/travels/{$uuid}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('travels', [
            'uuid' => $uuid,
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
