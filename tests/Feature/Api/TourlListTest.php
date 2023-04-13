<?php

namespace Tests\Feature\Api;

use App\Models\Tour;
use App\Models\Travel;
use Tests\TestCase;
use Illuminate\Testing\Fluent\AssertableJson;
use function collect;
use function config;
use function now;
use function range;

class TourlListTest extends TestCase
{
    /**
     * @test
     * @dataProvider pages
     */
    public function guests_can_access_all_tours_paginated_by_travel_slug(array $paginationData)
    {

        $travel = Travel::factory()->create();

        collect(range(1, 100))->each(function ($index) use ($travel) {
            Tour::factory()
                ->create([
                    'travelId' => $travel->id,
                    'startingDate' => now()->addMonths($index),
                    'endingDate' => now()->addMonths($index + 1),
                ]);
            ;
        });

        $tours = Tour::orderBy('startingDate')->get();

        $travelUuid = $travel->uuid;

        $travelSlug = $travel->slug;

        $response = $this->getJson("api/v1/travels/{$travelSlug}/tours")
            ->assertStatus(200);

        /**
         * Example:
         *   {
                "id": "2a0edc99-c9fe-4206-8da5-413586667a21",
                "travelId": "d408be33-aa6a-4c73-a2c8-58a70ab2ba4d",
                "name": "ITJOR20211101",
                "startingDate": "2021-11-01",
                "endingDate": "2021-11-09",
                "price": 199900
                },
                [...]
         *
         */

        $end = config('app.page_size') - 1;

        $response
            ->assertJson(fn(AssertableJson $json) => collect(range(start: 0, end: $end))->each(function (int $index) use ($travelUuid, $tours, $json) {

                $json->where("data.$index.id", $tours[$index]->uuid)
                    ->where("data.$index.travelId", $travelUuid)
                    ->where("data.$index.name", $tours[$index]->name)
                    ->where("data.$index.startingDate", $tours[$index]->startingDate)
                    ->where("data.$index.endingDate", $tours[$index]->endingDate)
                    ->where("data.$index.price", $tours[$index]->price)
                    ->etc();
            })
            );

        // test pagination
        $page = $paginationData['page'];

        $response2 = $this->getJson("api/v1/travels/{$travelSlug}/tours?page=$page")
            ->assertStatus(200);

        $start = ($page - 1) * config('app.page_size');

        $end = ($page * config('app.page_size')) - 1;

        // results must be next X Travels, where X is the page_size
        $response2->assertJson(fn(AssertableJson $json) => collect(range(start: $start, end: $end))->each(function (int $index) use ($page, $travelUuid, $tours, $json) {

            $jsonIndex = $index - config('app.page_size') * ($page - 1);

            $json->where("data.$jsonIndex.id", $tours[$index]->uuid)
                ->where("data.$jsonIndex.travelId", $travelUuid)
                ->where("data.$jsonIndex.name", $tours[$index]->name)
                ->where("data.$jsonIndex.startingDate", $tours[$index]->startingDate)
                ->where("data.$jsonIndex.endingDate", $tours[$index]->endingDate)
                ->where("data.$jsonIndex.price", $tours[$index]->price)
                ->etc();
            })
        );
    }

    public function pages(): array
    {
        return [
            [['page' => 1]],
            [['page' => 2]],
            [['page' => 3]],
            [['page' => 4]],
            [['page' => 5]],
            [['page' => 6]],
            [['page' => 7]],
            [['page' => 8]],
            [['page' => 9]],
            [['page' => 10]],
        ];
    }

}
