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
                $location = ApiHelper::getLocations()
                    ->first(function ($location) use ($registration) {
                        return $location['catalogusId'] === $registration->category &&
                            $location['start'] == $registration->starts_at->getTimestamp();
                    });

                if (! $location) {
                    continue;
                }

                $registration->updateFromLocation($location);
            }

            $registration->touch();

            if ($registration->starts_at->diffInMinutes(now()) < 30) {
                $registration->delete();
            }
            try {
                if (ApiHelper::registerLTicket($registration)) {
                    $registration->delete();
                }
            } catch (\Exception $e) {
            }
        }
    }
}
