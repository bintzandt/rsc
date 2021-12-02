<?php

namespace App\Helpers;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ApiHelper
{
    private const BASE_URL = 'https://publiek.usc.ru.nl/app/api/v1/?module=%s&method=%s';

    public static function signIn(User $user, string $password)
    {
        $url = sprintf(self::BASE_URL, 'user', 'logIn');

        $response = Http::asMultipart()->post($url, [
            'username' => $user->username,
            'password' => $password,
        ])->json();

        if (self::responseContainsError($response)) {
            throw new \Exception($response['error']);
        }

        $user->rsc_token = $response['token'];
        $user->rsc_id = $response['klantId'];
        $user->save();
    }

    public static function registerLTicket(Registration $registration)
    {
        $url = sprintf(self::BASE_URL, 'locatie', 'addLinschrijving');
        $user = $registration->user;

        $response = Http::asMultipart()
            ->post($url, [
                'klantId' => $user->rsc_id,
                'token' => $user->rsc_token,

                //                klantId:        186627
                //                token:          274eb165a7f5a1d6b2ca85f064874aba5cfec80d
                //                inschrijvingId: 695965
                //                poolId:         13
                //                laanbodId:      19
                //                start:          1626937200
                //                eind:           1626939900
                'inschrijvingId' => $registration->registration_id,
                'poolId' => $registration->pool_id,
                'laanbodId' => $registration->offer_id,
                'start' => $registration->starts_at->getTimestamp(),
                'eind' => $registration->ends_at->getTimestamp(),
            ])
            ->json();

        if (self::responseContainsError($response)) {
            throw new \Exception($response['error']);
        }

        return true;
    }

    public static function getLocations(): Collection
    {
        return Cache::remember('locations', 3600, function () {
            $url = sprintf(self::BASE_URL, 'locatie', 'getLocaties');

            if (! User::first()) {
                return collect([]);
            }

            return collect(Http::asMultipart()
                ->post($url, User::first()->toFormBody())
                ->json());
        });
    }

    public static function getCalendar(?User $user = null): Collection
    {
        if (! $user) {
            $user = Auth::user();
        }

        $url = sprintf(self::BASE_URL, 'agenda', 'getAgenda');

        return collect(Http::asMultipart()
            ->post($url, $user->toFormBody())
            ->json()
        );
    }

    private static function responseContainsError(array $responseData): bool
    {
        return array_key_exists('error', $responseData);
    }
}
