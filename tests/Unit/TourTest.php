<?php

namespace Tests\Unit;

use App\Models\Tour;
use App\Models\Travel;
use Tests\TestCase;
use function now;
use function rand;

class TourTest extends TestCase
{
    /**
     * @test
     */
    public function a_tour_belongs_to_a_travel()
    {
        $tours = Tour::factory()
            ->count(3)
//            ->for(Travel::factory())
            ->create();

        $this->assertInstanceOf(Travel::class, $tours[0]->travel);

        $this->assertInstanceOf(Travel::class, $tours[1]->travel);

        $this->assertInstanceOf(Travel::class, $tours[2]->travel);
    }

    /**
     * @test
     */
    public function tour_price_will_be_formatted_when_returned()
    {
        /** @var Tour $tour */
        $tour = Tour::factory()->create([
            'price' => 999
        ]);

        $this->assertDatabaseHas('tours', [
            'id' => $tour->id,
            'price' => 99900,
        ]);

        $this->assertEquals(999, $tour->price);
    }

    /**
     * @test
     */
    public function tours_can_be_filtered_by_travel_slug()
    {
        /** @var Travel $travelJOR */
        $travelJOR = Travel::factory()->create([
            'name' => 'Jordan 360°'
        ]);

        Tour::factory()
            ->count(10)
            ->create([
                'travelId' => $travelJOR->id
            ]);

        /** @var Travel $travelICE */
        $travelICE = Travel::factory()->create([
            'name' => 'Iceland: hunting for the Northern Lights'
        ]);

        Tour::factory()
            ->count(15)
            ->create([
                'travelId' => $travelICE->id
            ]);

        $this->assertCount(25, Tour::all());

        $this->assertCount(10, Tour::byTravelSlug($travelJOR->slug)->get());

        $this->assertCount(15, Tour::byTravelSlug($travelICE->slug)->get());
    }

    /**
     * @test
     */
    public function tours_can_be_filtered_by_price()
    {
        /** @var Travel $travelJOR */
        $travelJOR = Travel::factory()->create([
            'name' => 'Jordan 360°'
        ]);

        Tour::factory()
            ->count(5)
            ->create([
                'travelId' => $travelJOR->id,
                'price' => 1200
            ]);

        Tour::factory()
            ->count(10)
            ->create([
                'travelId' => $travelJOR->id,
                'price' => 900
            ]);

        Tour::factory()
            ->count(15)
            ->create([
                'travelId' => $travelJOR->id,
                'price' => 400
            ]);


        $this->assertCount(30, Tour::all());

        $this->assertCount(5, Tour::byPrice(start: 1000, end: 1300)->get());

        $this->assertCount(10, Tour::byPrice(start: 800, end: 1000)->get());

        $this->assertCount(15, Tour::byPrice(start: 300, end: 500)->get());

        // edge case
        $this->assertCount(15, Tour::byPrice(start: 300, end: 400)->get());

        // wide range
        $this->assertCount(30, Tour::byPrice(start: 100, end: 1400)->get());

        // mix with slug
        $this->assertCount(30, Tour::byPrice(start: 100, end: 1400)
                                                ->byTravelSlug($travelJOR->slug)
                                                ->get());
    }


    /**
     * @test
     */
    public function tours_can_be_filtered_by_starting_date()
    {
        /** @var Travel $travelJOR */
        $travelJOR = Travel::factory()->create([
            'name' => 'Jordan 360°'
        ]);

        Tour::factory()
            ->count(5)
            ->create([
                'travelId' => $travelJOR->id,
                'startingDate' => '2023-06-01',
                'endingDate' => '2023-06-31',
                'price' => 1400,
            ]);

        Tour::factory()
            ->count(10)
            ->create([
                'travelId' => $travelJOR->id,
                'startingDate' => '2023-07-01',
                'endingDate' => '2023-07-15',
                'price' => 900,
            ]);

        Tour::factory()
            ->count(15)
            ->create([
                'travelId' => $travelJOR->id,
                'startingDate' => '2023-08-01',
                'endingDate' => '2023-08-07',
                'price' => 400,
            ]);

        $this->assertCount(30, Tour::all());

        $this->assertCount(5, Tour::byStartingDate(from: '2023-05-01', to: '2023-06-31')->get());

        $this->assertCount(10, Tour::byStartingDate(from: '2023-07-01', to: '2023-07-16')->get());

        $this->assertCount(15, Tour::byStartingDate(from: '2023-08-01', to: '2023-08-08')->get());

        // edge case
        $this->assertCount(15, Tour::byStartingDate(from: '2023-05-01', to: '2023-07-01')->get());

        // wide range
        $this->assertCount(30, Tour::byStartingDate(from: '2023-05-01', to: '2023-10-01')->get());

        // mix with slug and price
        $this->assertCount(25, Tour::byStartingDate(from: '2023-05-01', to: '2023-10-01')
                                                ->byPrice(start: 400, end: 1000)
                                                ->byTravelSlug($travelJOR->slug)
                                                ->get());
    }
}
