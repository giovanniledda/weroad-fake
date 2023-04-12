<?php

namespace App\Observers;

use App\Models\Travel;
use Illuminate\Support\Str;

class TravelObserver
{
    /**
     * Handle the Travel "creating" event.
     *
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
     * @return void
     */
    public function created(Travel $travel)
    {
        $travel->initializeMoods();
    }

    /**
     * Handle the Travel "updating" event.
     *
     * @return void
     */
    public function updating(Travel $travel)
    {
        $travel->slug = Str::slug($travel->name);
    }

    /**
     * Handle the Travel "deleted" event.
     *
     * @return void
     */
    public function deleted(Travel $travel)
    {
        //
    }

    /**
     * Handle the Travel "restored" event.
     *
     * @return void
     */
    public function restored(Travel $travel)
    {
        //
    }

    /**
     * Handle the Travel "force deleted" event.
     *
     * @return void
     */
    public function forceDeleted(Travel $travel)
    {
        //
    }
}
