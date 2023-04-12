<?php

namespace App\Observers;

use App\Models\Travel;
use Illuminate\Support\Str;

class TravelObserver
{
    /**
     * Handle the Travel "creating" event.
     *
     * @param  \App\Models\Travel  $travel
     * @return void
     */
    public function creating(Travel $travel)
    {
        $travel->slug = Str::slug($travel->name);

//        $travel->saveQuietly();
    }

    /**
     * Handle the Travel "created" event.
     *
     * @param  \App\Models\Travel  $travel
     * @return void
     */
    public function created(Travel $travel)
    {
        $travel->initializeMoods();
    }

    /**
     * Handle the Travel "updating" event.
     *
     * @param  \App\Models\Travel  $travel
     * @return void
     */
    public function updating(Travel $travel)
    {
        $travel->slug = Str::slug($travel->name);

    }

    /**
     * Handle the Travel "deleted" event.
     *
     * @param  \App\Models\Travel  $travel
     * @return void
     */
    public function deleted(Travel $travel)
    {
        //
    }

    /**
     * Handle the Travel "restored" event.
     *
     * @param  \App\Models\Travel  $travel
     * @return void
     */
    public function restored(Travel $travel)
    {
        //
    }

    /**
     * Handle the Travel "force deleted" event.
     *
     * @param  \App\Models\Travel  $travel
     * @return void
     */
    public function forceDeleted(Travel $travel)
    {
        //
    }
}
