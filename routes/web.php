<?php

use App\Helpers\ApiHelper;
use App\Models\Registration;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth']], function () {
    Route::view('/', 'index', [
        'locations' => ApiHelper::getLocations()->map(function ($location) {
            return [
                'id' => $location['planregelId'],
                'name' => $location['naam'].' - '.Carbon::createFromTimestamp($location['start'])
                        ->toDateTimeString('minute'),
                'category' => $location['catalogusId'],
            ];
        })->sortBy('name'),
        'categories' => ApiHelper::getLocations()->pluck('catalogusId')->unique()->sort()->toArray(),
    ]);

    Route::post('/', function (Request $request) {
        $location = ApiHelper::getLocations()->firstWhere('planregelId', '=', $request->post('location'));
        if ($location) {
            $registration = new Registration;
            $registration->category = $location['catalogusId'];
            $registration->starts_at = $location['start'];
            $registration->user_id = Auth::user()->id;
            $registration->save();
        }
        return redirect()->back();
    });

    Route::view('/custom', 'custom',
        ['categories' => ApiHelper::getLocations()->pluck('catalogusId')->unique()->sort()->toArray()]
    )->name('custom');

    Route::post('/custom', function(Request $request){
        $registration = new Registration;
        $registration->category = $request->post('category');
        $registration->starts_at = $request->post('starts_at');
        $registration->user_id = Auth::user()->id;
        $registration->save();

        return redirect()->route('registrations');
    });

    Route::get('/registrations', function () {
        return view('registrations', [
            'registrations' => ApiHelper::getCalendar(),
            'upcomingRegistrations' => Registration::where('user_id', Auth::user()->id)->orderBy('starts_at')->get(),
        ]);
    })->name('registrations');

    Route::get('/registrations/{registration}/delete', function (Registration $registration) {
        if (! $registration->user_id === Auth::user()->id) {
            throw new Exception('User is not allowed to delete this registration');
        }

        $registration->delete();

        return redirect()->back();
    })->name('registrations.delete');
});

Route::view('/login', 'login', ['users' => User::all()])->name('login');
Route::post('/login', function (Request $request) {
    Auth::loginUsingId($request->post('user', true));
    $request->session()->regenerate();

    return redirect()->intended();
});
Route::get('/logout', function (Request $request) {
    $request->session()->regenerate(true);
    return redirect()->route('login');
})->name('logout');

Route::view('/register', 'register')->name('register');
Route::post('/register', function (Request $request) {
    $username = $request->post('username');
    $password = $request->post('password');

    $user = new User;
    $user->username = $username;

    ApiHelper::signIn($user, $password);

    return redirect()->route('login');
});
