<?php

namespace Tests\Feature\Api;

use App\Models\Tour;
use App\Models\Travel;
use function collect;
use function config;
use Illuminate\Testing\Fluent\AssertableJson;
use function now;
use function range;
use Tests\TestCase;

class TourlListTest extends TestCase
{
    /**
     * @test
     *
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
        });

        $tours = Tour::orderBy('startingDate')->get();

        $travelUuid = $travel->uuid;

        $travelSlug = $travel->slug;

        // testing wrong slug
        $this->getJson('api/v1/travels/i-am-an-invalid-slug/tours')
            ->assertStatus(404);

        $response = $this->getJson("api/v1/travels/{$travelSlug}/tours")
            ->assertStatus(200);

        /**
         * Example:
         *   {
         * "id": "2a0edc99-c9fe-4206-8da5-413586667a21",
         * "travelId": "d408be33-aa6a-4c73-a2c8-58a70ab2ba4d",
         * "name": "ITJOR20211101",
         * "startingDate": "2021-11-01",
         * "endingDate": "2021-11-09",
         * "price": 199900
         * },
         * [...]
         */
        $end = config('app.page_size') - 1;

        $response
            ->assertJson(fn (AssertableJson $json) => collect(range(start: 0, end: $end))->each(function (int $index) use ($travelUuid, $tours, $json) {
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
        $response2->assertJson(fn (AssertableJson $json) => collect(range(start: $start, end: $end))->each(function (int $index) use ($page, $travelUuid, $tours, $json) {
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

    /**
     * @test
     */
//     * @dataProvider pages
    public function guests_can_access_all_tours_paginated_by_travel_slug_filtered_by_price(/*array $paginationData*/)
    {
        $travel = Travel::factory()->create();

        // 10 tours at price 300
        collect(range(1, 10))->each(function ($index) use ($travel) {
            Tour::factory()
                ->create([
                    'travelId' => $travel->id,
                    'startingDate' => now()->addMonths($index),
                    'endingDate' => now()->addMonths($index + 1),
                    'price' => 300,
                ]);
        });

        // 15 tours at price 900
        collect(range(1, 15))->each(function ($index) use ($travel) {
            Tour::factory()
                ->create([
                    'travelId' => $travel->id,
                    'startingDate' => now()->addMonths($index),
                    'endingDate' => now()->addMonths($index + 1),
                    'price' => 900,
                ]);
        });

        $tours = Tour::orderBy('startingDate')->get();

        $travelUuid = $travel->uuid;

        $travelSlug = $travel->slug;

        // standard filter
        $response = $this->getJson("api/v1/travels/{$travelSlug}/tours?priceFrom=200&priceTo=400")
            ->assertStatus(200);

        $end = config('app.page_size') - 1;

        $response
            ->assertJson(fn (AssertableJson $json) => collect(range(start: 0, end: $end))->each(function (int $index) use ($json) {
                $json->where("data.$index.price", 300)
                    ->whereNot("data.$index.price", 900)
                    ->etc();
            }));

        // standard filter 2
        $response = $this->getJson("api/v1/travels/{$travelSlug}/tours?priceFrom=700&priceTo=1000")
            ->assertStatus(200);

        $end = config('app.page_size') - 1;

        $response
            ->assertJson(fn (AssertableJson $json) => collect(range(start: 0, end: $end))->each(function (int $index) use ($json) {
                $json->where("data.$index.price", 900)
                    ->whereNot("data.$index.price", 300)
                    ->etc();
            }));

        // missing "from" filter
        $response = $this->getJson("api/v1/travels/{$travelSlug}/tours?priceTo=500")
            ->assertStatus(200);

        $end = config('app.page_size') - 1;

        $response
            ->assertJson(fn (AssertableJson $json) => collect(range(start: 0, end: $end))->each(function (int $index) use ($json) {
                $json->where("data.$index.price", 300)
                    ->whereNot("data.$index.price", 900)
                    ->etc();
            }));

        // todo: missing "to" filter
    }

    /**
     * @test
     */
//     * @dataProvider pages
    public function guests_can_access_all_tours_paginated_by_travel_slug_filtered_by_starting_date(/*array $paginationData*/)
    {
        $travel = Travel::factory()->create();

        // 10 tours in june 2024
        collect(range(1, 10))->each(function ($index) use ($travel) {
            Tour::factory()
                ->create([
                    'travelId' => $travel->id,
                    'startingDate' => '2024-06-01',
                    'endingDate' => '2024-06-30',
                    'price' => 300,
                ]);
        });

        // 15 tours in june 2025
        collect(range(1, 15))->each(function ($index) use ($travel) {
            Tour::factory()
                ->create([
                    'travelId' => $travel->id,
                    'startingDate' => '2025-06-01',
                    'endingDate' => '2025-06-30',
                    'price' => 900,
                ]);
        });

        $tours = Tour::orderBy('startingDate')->get();

        $travelUuid = $travel->uuid;

        $travelSlug = $travel->slug;

        // standard filter
        $response = $this->getJson("api/v1/travels/{$travelSlug}/tours?dateFrom=2024-05-01&dateTo=2024-07-01")
            ->assertStatus(200);

        $end = config('app.page_size') - 1;

        $response
            ->assertJson(fn (AssertableJson $json) => collect(range(start: 0, end: $end))->each(function (int $index) use ($json) {
                $json->where("data.$index.startingDate", '2024-06-01')
                    ->where("data.$index.endingDate", '2024-06-30')
                    ->whereNot("data.$index.startingDate", '2025-06-01')
                    ->whereNot("data.$index.endingDate", '2025-06-30')
                    ->etc();
            }));

        // standard filter 2
        $response = $this->getJson("api/v1/travels/{$travelSlug}/tours?dateFrom=2025-05-01&dateTo=2025-07-01")
            ->assertStatus(200);

        $end = config('app.page_size') - 1;

        $response
            ->assertJson(fn (AssertableJson $json) => collect(range(start: 0, end: $end))->each(function (int $index) use ($json) {
                $json->where("data.$index.startingDate", '2025-06-01')
                    ->where("data.$index.endingDate", '2025-06-30')
                    ->whereNot("data.$index.startingDate", '2024-06-01')
                    ->whereNot("data.$index.endingDate", '2024-06-30')
                    ->etc();
            }));

        // missing "from" filter
        $response = $this->getJson("api/v1/travels/{$travelSlug}/tours?dateTo=2024-12-31")
            ->assertStatus(200);

        $end = config('app.page_size') - 1;

        $response
            ->assertJson(fn (AssertableJson $json) => collect(range(start: 0, end: $end))->each(function (int $index) use ($json) {
                $json->where("data.$index.startingDate", '2024-06-01')
                    ->where("data.$index.endingDate", '2024-06-30')
                    ->whereNot("data.$index.startingDate", '2025-06-01')
                    ->whereNot("data.$index.endingDate", '2025-06-30')
                    ->etc();
            }));

        // todo: missing "to" filter
    }

    /**
     * @test
     */
    public function tour_filters_must_respect_some_conventions()
    {
        $travel = Travel::factory()->create();

        $travelSlug = $travel->slug;

        // priceFrom must be numeric
        $this->getJson("api/v1/travels/{$travelSlug}/tours?priceFrom=blablabla")
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('priceFrom');

        // priceTo must be numeric
        $this->getJson("api/v1/travels/{$travelSlug}/tours?priceFrom=200&priceTo=blablabla")
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('priceTo');

        // priceTo >= priceFrom
        $this->getJson("api/v1/travels/{$travelSlug}/tours?priceFrom=200&priceTo=50")
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('priceTo');

        // dateFrom must be a valid (Y-m-d) date
        $this->getJson("api/v1/travels/{$travelSlug}/tours?dateFrom=01-01-2025")
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('dateFrom');

        // dateTo must be a valid (Y-m-d) date
        $this->getJson("api/v1/travels/{$travelSlug}/tours?dateTo=2025-05-0109")
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('dateTo');

        // dateTo >= dateFrom
        $this->getJson("api/v1/travels/{$travelSlug}/tours?dateFrom=2025-05-01&dateTo=2024-07-01")
            ->assertStatus(422)
            ->assertJsonValidationErrorFor('dateTo');
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
