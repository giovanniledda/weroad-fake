<?php

namespace Tests\Feature\Api;

use App\Models\Travel;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use function collect;
use function config;
use function range;

class TravelListTest extends TestCase
{
    /**
     * @test
     */
    public function guests_can_access_all_public_travels_paginated()
    {

        $travels = Travel::factory()
            ->count(100)
            ->create();

        $response = $this->getJson('api/v1/travels')
            ->assertStatus(200);

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
                    },
                    [...]
             */

        $end = config('app.page_size') - 1;

        $response
            ->assertJson(fn(AssertableJson $json) => collect(range(start: 0, end: $end))->each(function (int $index) use ($travels, $json) {

                $json->where("data.$index.id", $travels[$index]->uuid)
                    ->where("data.$index.slug", $travels[$index]->slug)
                    ->where("data.$index.name", $travels[$index]->name)
                    ->where("data.$index.description", $travels[$index]->description)
                    ->where("data.$index.numberOfDays", $travels[$index]->days)
                    ->where("data.$index.moods", $travels[$index]->moods['moods'])
                    ->etc();
            })
            );

        // test pagination
        $page = 4;

        $response2 = $this->getJson('api/v1/travels?page='.$page)
            ->assertStatus(200);

        $start = ($page - 1) * config('app.page_size');

        $end = ($page * config('app.page_size')) - 1;

        // results must be next X Travels, where X is the page_size
        $response2->assertJson(fn(AssertableJson $json) => collect(range(start: $start, end: $end))->each(function (int $index) use ($page, $travels, $json) {

            $jsonIndex = $index - config('app.page_size') * ($page - 1);

            $json->where("data.$jsonIndex.id", $travels[$index]->uuid)
                ->where("data.$jsonIndex.slug", $travels[$index]->slug)
                ->where("data.$jsonIndex.name", $travels[$index]->name)
                ->where("data.$jsonIndex.description", $travels[$index]->description)
                ->where("data.$jsonIndex.numberOfDays", $travels[$index]->days)
                ->where("data.$jsonIndex.moods", $travels[$index]->moods['moods'])
                ->etc();
            })
        );
    }


}
