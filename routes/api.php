<?php

use App\Http\Controllers\Auth\userAuthController;
use App\Http\Controllers\EndUser\CityController;
use App\Http\Controllers\EndUser\HotelController;
use App\Http\Controllers\EndUser\TourController;
use App\Http\Controllers\EndUser\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['prefix' => '/v1'], function () {

    //global apis
    Route::post('/sendOtp', [userAuthController::class, 'getPhone']);
    Route::post('/verifyOtp', [userAuthController::class, 'verifyOtp']);

    //auth apis
    Route::group(
        ['middleware' => 'auth:api'],
        function () {
            //get uer info
            Route::get('/userInfo', [userAuthController::class, 'userInfo']);
            //set user info
            Route::post('/userInfo', [userAuthController::class, 'setUserInfo']);

            Route::prefix('/user')->group(
                function () {

                        Route::get('/passengers', [UserController::class, 'passengers']);

                        //transactions
                        Route::get('/transactions', [UserController::class, 'transactions']);

                        //save passenger
                        Route::post('/savePassenger', [UserController::class, 'savePassenger']);


                    }

            );


        }
    );



    //hotels
    Route::get('/hotels', [HotelController::class, 'index']);
    //hotel item
    Route::get('/hotels/{id}', [HotelController::class, 'show']);

    Route::prefix('/tours')->group(
        function () {
            //suggestHotel
            Route::post('/suggest/hotel', [TourController::class, 'suggestHotel']);
            Route::post('/suggest/vehicle', [TourController::class, 'suggestVehicle']);
            Route::post('/calculatePrice', [TourController::class, 'calculatePrice']);
            //tour services
            Route::get('/services', [TourController::class, 'tourServices']);


            Route::group(
                ['middleware' => 'auth:api'],
                function () {
                        //save
                        Route::post('/save', [TourController::class, 'saveTour']);

                        //save passengers
                        Route::post('/savePassengers', [TourController::class, 'savePassengers']);

                        //user tours
                        Route::get('/user', [TourController::class, 'userTours']);

                        //view
                        Route::get('/{id}', [TourController::class, 'viewTour']);

                    }
            );
        }
    );

    //suggest cities
    Route::get('/cities/available', [CityController::class, 'available']);
});