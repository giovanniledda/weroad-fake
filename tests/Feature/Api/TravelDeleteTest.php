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
}
