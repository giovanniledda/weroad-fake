<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Validation\Rule;
use function config;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTourRequest;
use App\Http\Requests\StoreTravelRequest;
use App\Http\Requests\UpdateTravelRequest;
use App\Http\Resources\TourResource;
use App\Http\Resources\TravelResource;
use App\Models\Travel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Fluent;
use Illuminate\Validation\ValidationException;

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
        // TODO: validate filters con una FormRequest
        $validator = Validator::make($request->only(['priceFrom', 'priceTo', 'dateFrom', 'dateTo', 'sortByPrice']),
            [
                'priceFrom' => 'numeric|nullable|sometimes',
                'priceTo' => 'numeric|nullable|sometimes',
                'dateFrom' => 'date_format:Y-m-d|nullable|sometimes',
                'dateTo' => 'date_format:Y-m-d|nullable|sometimes|after:dateFrom',
                'sortByPrice' => 'sometimes|in:asc,desc',
            ])->sometimes('priceTo', 'gte:priceFrom', function (Fluent $input) {
            return ! empty($input->priceFrom);
        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

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
