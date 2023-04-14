<?php

namespace Tests\Feature\Api;

use App\Models\Tour;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TourDeleteTest extends TestCase
{
    /**
     * @test
     */
    public function guests_cannot_delete_tours()
    {
        $tour = Tour::factory()->create();

        $uuid = $tour->uuid;

        $this->deleteJson("api/v1/tours/{$uuid}")
            ->assertStatus(401);
    }

    /**
     * @test
     */
    public function editors_cannot_delete_tours()
    {
        $tour = Tour::factory()->create();

        $uuid = $tour->uuid;

        $editor = $this->createEditor();

        Sanctum::actingAs($editor);

        $response = $this->delete("api/v1/tours/{$uuid}")
            ->assertStatus(401);
    }

    /**
     * @test
     */
    public function admins_can_delete_tours()
    {
        $tour = Tour::factory()->create();

        $uuid = $tour->uuid;

        $admin = $this->createAdmin();

        Sanctum::actingAs($admin);

        $this->assertDatabaseHas('tours', [
            'uuid' => $uuid,
        ]);

        $this->deleteJson("api/v1/tours/{$uuid}")
            ->assertStatus(204);

        $this->assertDatabaseMissing('tours', [
            'uuid' => $uuid,
        ]);
    }
}
