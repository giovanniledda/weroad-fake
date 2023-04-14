<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tour;

class TourController extends Controller
{
    /**
     * Delete the specified resource
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Tour $tour)
    {
        $tour->delete();

        return response()
            ->json([])
            ->setStatusCode(204);
    }
}
