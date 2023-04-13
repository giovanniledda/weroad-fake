<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTourRequest;
use App\Http\Requests\StoreTravelRequest;
use App\Http\Requests\UpdateTravelRequest;
use App\Http\Resources\TourResource;
use App\Http\Resources\TravelResource;
use App\Models\Tour;
use App\Models\Travel;
use function ray;

class TravelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
     * Display the specified resource.
     *
     * @param  \App\Models\Travel  $travel
     * @return \Illuminate\Http\Response
     */
    public function show(Travel $travel)
    {
        //
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

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Travel  $travel
     * @return \Illuminate\Http\Response
     */
    public function destroy(Travel $travel)
    {
        //
    }


}
