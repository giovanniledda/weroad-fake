<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTourRequest;
use App\Http\Requests\StoreTravelRequest;
use App\Http\Requests\UpdateTravelRequest;
use App\Http\Resources\TourResource;
use App\Http\Resources\TravelResource;
use App\Models\Travel;
use App\Services\FiltersValidator;
use Illuminate\Http\Request;

class TravelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        return TravelResource::collection(Travel::getPaginatedList());
    }

    /**
     * Store a newly created resource in storage.
     *
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
     * @return TravelResource
     */
    public function update(UpdateTravelRequest $request, Travel $travel)
    {
        $validated = $request->validated();

        $travel->update($validated);

        return new TravelResource($travel);
    }

    /**
     * Delete the specified resource
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Travel $travel)
    {
        $travel->delete();

        return response()
            ->json([])
            ->setStatusCode(204);
    }

    public function createTour(StoreTourRequest $request, Travel $travel)
    {
        $validated = $request->validated();

        $tour = $travel->tours()->create($validated);

        return new TourResource($tour);
    }

    public function getTours(Request $request, Travel $travel, FiltersValidator $validator)
    {
        $validator->validate($request->only(['priceFrom', 'priceTo', 'dateFrom', 'dateTo', 'sortByPrice']));

        $orders = $travel
            ->tours()
            ->byPrice(start: $request->get('priceFrom'), end: $request->get('priceTo'))
            ->byStartingDate(from: $request->get('dateFrom'), to: $request->get('dateTo'))
            ->when($request->has('sortByPrice'), function ($builder) use ($request) {
                $builder->orderBy('price', $request->get('sortByPrice'));
            })
            ->orderBy('startingDate')
            ->fastPaginate(config('app.page_size'));

        return TourResource::collection($orders);
    }
}
