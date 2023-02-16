<?php

use App\Http\Controllers\AgencyController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\HotelServiceController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\RoomServiceController;
use App\Http\Controllers\TourServiceController;
use App\Http\Controllers\TransportAgenciesController;
use App\Http\Controllers\TransportVehicleController;
use App\Http\Controllers\UserTourController;
use App\Http\Controllers\WatcherController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Route::get('/dashboard', function () {
//     return Inertia::render('Dashboard');
// })->middleware(['auth', 'verified' , 'adminMiddleware'])->name('dashboard');

Route::group(['prefix' => 'dashboard', 'middleware' => ['auth', 'verified', 'adminMiddleware']], function () {

    Route::get(
        '/',
        function () {
            return Inertia::render('Dashboard');
        }
    )->name('dashboard');

    Route::group(
        ['prefix' => 'agencies'],
        function () {
            Route::get('/', [AgencyController::class, 'index'])->name('agencies');
            Route::get('/create', [AgencyController::class, 'create'])->name('createAgency');
            Route::post('/create', [AgencyController::class, 'store'])->name('storeAgency');
            Route::delete('/agencies/{id}', [AgencyController::class, 'destroy'])->name('agencies.destroy');
            Route::get('/agencies/{id}', [AgencyController::class, 'edit'])->name('agencies.edit');
            Route::put('/agencies/{id}', [AgencyController::class, 'update'])->name('agencies.update');
        }
    );

    Route::resource('/tourServices', TourServiceController::class);
    Route::resource('/watchers', WatcherController::class);
    Route::resource('/transportCompanies', TransportAgenciesController::class);
    Route::post('/transportCompanies/{id}/active', [TransportAgenciesController::class, 'active'])->name("transportCompanies.active");
    Route::post('/loadCity', [AgencyController::class, 'loadCity'])->name('loadCity');


    Route::post('/sendWatcherLink2', [WatcherController::class, 'sendWatcherLink'])->name('sendWatcherLink');
    //transport vehicles resource
    Route::resource('/transportVehicles', TransportVehicleController::class);
    Route::post('/transportVehicles/{id}/active', [TransportVehicleController::class, 'active'])->name("transportVehicles.active");

    Route::resource('/hotels', HotelController::class);
    Route::post('/hotels/{id}/active', [HotelController::class, 'active'])->name("hotels.active");
    Route::get('/hotels/{id}/gallery', [HotelController::class, 'gallery'])->name("hotels.gallery");
    //save file
    Route::post('/saveFile/{hotel_id}', [HotelController::class, 'saveFile'])->name('saveFile');

    //delete file
    Route::delete('/deleteFile/{id}', [HotelController::class, 'deleteFile'])->name('deleteFile');

    Route::resource('/hotelServices', HotelServiceController::class);
    Route::resource('/roomServices', RoomServiceController::class);


    //load transport companies
    Route::post('/loadTransportCompanies', [TransportVehicleController::class, 'loadTransportCompanies'])->name('loadTransportCompanies');
    //load transport vehicles
    Route::post('/loadTransportVehicles', [TransportVehicleController::class, 'loadTransportVehicles'])->name('loadTransportVehicles');


    Route::group(
        ['prefix' => 'userTour'],
        function () {
            //index
            Route::get('/', [UserTourController::class, 'index'])->name('userTour.index');
            Route::get('/{id}/edit', [UserTourController::class, 'edit'])->name('userTour.edit');
            //free hotels
            Route::post('/freeHotels', [UserTourController::class, 'freeHotels'])->name('userTour.freeHotels');
            Route::post('/freeDepartureVehicles', [UserTourController::class, 'freeDepartureVehicles'])->name('userTour.freeDepartureVehicles');
            Route::post('/freeArrivalVehicles', [UserTourController::class, 'freeArrivalVehicles'])->name('userTour.freeArrivalVehicles');
            //calculate price
            Route::post('/calculatePrice', [UserTourController::class, 'calculatePrice'])->name('userTour.calculatePrice');

            //update
            Route::put('/{id}', [UserTourController::class, 'update'])->name('userTour.update');

            //fail
            Route::post('/{id}/fail', [UserTourController::class, 'fail'])->name('userTour.fail');

        }
    );
});
Route::post('/createLink', [WatcherController::class, 'createLink'])->name('createLink');

Route::get('/printWatcher/{hash}', [WatcherController::class, 'showUserWatcher'])->name('showUserWatcher');

Route::group(['middleware' => ['otpMiddleware']], function () {

    Route::get('/loginOtp', [OtpController::class, 'loginOtp'])->name('loginOtp');
    Route::post('/loginOtp', [OtpController::class, 'doLoginOtp'])->name('doLoginOtp');
    Route::post('/confirmOtp', [OtpController::class, 'confirmOtp'])->name('confirmOtp');
});

Route::group(['prefix' => 'agencyDashboard', 'middleware' => ['auth', 'verified', 'agencyMiddleware']], function () {
    Route::get('/', [AgencyController::class, 'agencyDashboard'])->name('agencyDashboard');
    Route::get('/setting', [AgencyController::class, 'agencySetting'])->name('agencySetting');
    Route::post('/setting', [AgencyController::class, 'agencySettingUpdate'])->name('agencySetting.update');
    Route::get('/watchers', [AgencyController::class, 'loadWatchers'])->name('loadWatchers');
    Route::get('/watchers/{id}', [AgencyController::class, 'loadSingleWatcher'])->name('loadSingleWatcher');

    Route::post('/saveWatcherMarkup', [WatcherController::class, 'saveWatcherMarkup'])->name('saveWatcherMarkup');

});

require __DIR__ . '/auth.php';