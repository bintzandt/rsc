<?php

namespace App\Jobs;

use App\Helpers\ApiHelper;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

class TryToRegister implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $registrations = Registration::all();

        foreach ($registrations as $registration) {
            if (! $registration->isComplete()) {
                $location = $this->getLocations()
                    ->first(fn($location
                    ) => $location['catalogusId'] === $registration->category &&
                        $location['start'] == $registration->starts_at->getTimestamp()
                    );

                if (! $location) {
                    continue;
                }

                $registration->updateFromLocation($location);
            }

            if ($registration->starts_at->diffInMinutes(now()) < 30) {
                $registration->delete();
            }

            if (ApiHelper::registerLTicket(User::first(), $registration)) {
                $registration->delete();
            }
        }
    }

    public function getLocations()
    {
        return Cache::remember('locations', 3600, function () {
            return collect(ApiHelper::getLocations(User::first()));
        });
    }
}
