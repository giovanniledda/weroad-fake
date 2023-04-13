<?php

namespace Tests\Unit;

use App\Enums\Mood;
use App\Models\Travel;
use Illuminate\Support\Str;
use Tests\TestCase;

class TravelTest extends TestCase
{
    /**
     * @test
     */
    public function a_travel_has_name_based_slug()
    {
        /** @var Travel $travel */
        $travel = Travel::factory()->create();

        $this->assertDatabaseHas('travels', [
            'id' => $travel->id,
            'slug' => Str::slug($travel->name),
        ]);
    }

    /**
     * @test
     */
    public function a_travel_could_be_public()
    {
        /** @var Travel $travel */
        $travel = Travel::factory()->create();

        $this->assertTrue($travel->isPublic);
    }

    /**
     * @test
     */
    public function a_public_travel_could_be_made_private()
    {
        /** @var Travel $travel */
        $travel = Travel::factory()->create();

        $travel->unpublish();

        $this->assertFalse($travel->isPublic);
    }

    /**
     * @test
     */
    public function a_travel_could_be_private()
    {
        /** @var Travel $travel */
        $travel = Travel::factory()->private()->create();

        $this->assertFalse($travel->isPublic);
    }

    /**
     * @test
     */
    public function a_private_travel_could_be_published()
    {
        /** @var Travel $travel */
        $travel = Travel::factory()->private()->create();

        $travel->publish();

        $this->assertTrue($travel->isPublic);
    }

    /**
     * @test
     */
    public function public_and_private_travels_can_be_filtered()
    {
        $publicTravels = Travel::factory()->count(10)->create();

        $privateTravels = Travel::factory()->private()->count(5)->create();

        $this->assertCount(10, Travel::public()->get());

        $this->assertCount(5, Travel::private()->get());
    }

    /**
     * @test
     */
    public function travels_nights_are_days_plus_one()
    {
        /** @var Travel $travel */
        $travel = Travel::factory()->create([
            'days' => 6,
        ]);

        $travel->refresh();

        $this->assertEquals(7, $travel->nights);
    }

    /**
     * @test
     */
    public function a_travel_has_moods_array()
    {
        /** @var Travel $travel */
        $travel = Travel::factory()->create();

        // Moods array should be like this
        $moods = [
            'moods' => [
                'nature' => 0,
                'relax' => 0,
                'history' => 0,
                'culture' => 0,
                'party' => 0,
            ],
        ];

        $this->assertEquals($moods, $travel->moods);
    }

    /**
     * @test
     */
    public function travel_moods_can_be_modified()
    {
        /** @var Travel $travel */
        $travel = Travel::factory()->create();

        $travel->updateMood(Mood::Nature, 100);

        $travel->updateMood(Mood::Relax, 30);

        $travel->updateMood(Mood::History, 100);

        $travel->updateMood(Mood::Culture, 90);

        $travel->updateMood(Mood::Party, 20);

        // Moods array should be like this
        $moods = [
            'moods' => [
                'nature' => 100,
                'relax' => 30,
                'history' => 100,
                'culture' => 90,
                'party' => 20,
            ],
        ];

        $travel->refresh();

        $this->assertEquals($moods, $travel->moods);
    }
}
