<?php

namespace App\Helpers;

use App\Models\Registration;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class ApiHelper
{
    private const BASE_URL = 'https://publiek.usc.ru.nl/app/api/v1/?module=%s&method=%s';

    public static function signIn(User $user)
    {
        $url = sprintf(self::BASE_URL, 'user', 'logIn');

        $response = Http::asMultipart()->post($url, [
            'username' => $user->username,
            'password' => $user->password,
        ])->json();

        if (self::responseContainsError($response)) {
            throw new \Exception($response['error']);
        }

        $user->rsc_token = $response['token'];
        $user->rsc_id = $response['klantId'];
        $user->name = $response['voornaam'].' '.$response['voorvoegsels'].$response['achternaam'];
        $user->save();
    }

    public static function registerLTicket(User $user, Registration $registration)
    {
        $url = sprintf(self::BASE_URL, 'locatie', 'addLinschrijving');

        if (! $user->rsc_id) {
            self::signIn($user);
        }

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

        var_dump($response);
        return true;
    }

    public static function getLocations(User $user): array
    {
        $url = sprintf(self::BASE_URL, 'locatie', 'getLocaties');

        return Http::asMultipart()
            ->post($url, $user->toFormBody())
            ->json();
    }

    private static function responseContainsError(array $responseData): bool
    {
        return array_key_exists('error', $responseData);
    }
}
