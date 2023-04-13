<?php

namespace Tests\Feature\Api;

use App\Models\Travel;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TravelListTest extends TestCase
{
    /**
     * @test
     */
    public function guests_can_access_all_public_travels()
    {

        $travels = Travel::factory()
            ->count(10)
            ->create();

        $response = $this->getJson('api/v1/travels')
            ->assertStatus(200);

        ray($response->json());
    }


}
