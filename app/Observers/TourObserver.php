<?php

namespace App\Observers;

use App\Models\Tour;
use function is_null;

class TourObserver
{
    /**
     * Handle the Tour "created" event.
     *
     * @return void
     */
    public function created(Tour $tour)
    {
        if (is_null($tour->endingDate)) {
            $tour->endingDate = $tour->getCalculatedEndingDate();

            $tour->saveQuietly();
        }
    }

    /**
     * Handle the Tour "updated" event.
     *
     * @return void
     */
    public function updated(Tour $tour)
    {
        if (is_null($tour->endingDate)) {
            $tour->endingDate = $tour->getCalculatedEndingDate();

            $tour->saveQuietly();
        }
    }

    /**
     * Handle the Tour "deleted" event.
     *
     * @return void
     */
    public function deleted(Tour $tour)
    {
        //
    }

    /**
     * Handle the Tour "restored" event.
     *
     * @return void
     */
    public function restored(Tour $tour)
    {
        //
    }

    /**
     * Handle the Tour "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Tour $tour)
    {
        //
    }
}
