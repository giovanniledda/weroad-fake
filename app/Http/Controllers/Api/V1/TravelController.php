<?php

namespace App\Http\Controllers\Api\V1;

use function config;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTourRequest;
use App\Http\Requests\StoreTravelRequest;
use App\Http\Requests\UpdateTravelRequest;
use App\Http\Resources\TourResource;
use App\Http\Resources\TravelResource;
use App\Models\Travel;
use function ray;

class TravelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return TravelResource::collection(Travel::public()->fastPaginate(config('app.page_size')));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTravelRequest  $request
     * @return TravelResource|\Illuminate\Http\Response
     */
    public function store(StoreTravelRequest $request)
    {
        $validated = $request->validated();

        $travel = Travel::create($validated);

        return new TravelResource($travel);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTravelRequest  $request
     * @param  \App\Models\Travel  $travel
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTravelRequest $request, Travel $travel)
    {
        $validated = $request->validated();

        $travel->update($validated);

        return new TravelResource($travel);
    }

    public function createTour(StoreTourRequest $request, Travel $travel)
    {
        $validated = $request->validated();

        $tour = $travel->tours()->create($validated);

        return new TourResource($tour);
    }

    public function getTours(Request $request, Travel $travel)
    {
//        ray('slug', $travel->slug);
//        ray('tours', $travel->tours()->orderBy('startingDate')->get());

        // TODO: validate filters con una FormRequest

        $orders = $travel
                    ->tours()
                    ->when($request->has('priceFrom') || $request->has('priceTo'), function ($builder) use ($request) {
                        $builder->byPrice(start: $request->get('priceFrom'), end: $request->get('priceTo'));
                    })
                    ->when($request->has('dateFrom') || $request->has('dateTo'), function ($builder) use ($request) {
                        $builder->byStartingDate(from: $request->get('dateFrom'), to: $request->get('dateTo'));
                    })
                    ->orderBy('startingDate')
                    ->fastPaginate(config('app.page_size'));

        return TourResource::collection($orders);
    }

}
