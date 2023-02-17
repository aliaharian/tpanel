<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use App\Models\TourRoom;
use App\Models\TourService;
use App\Models\TourStatus;
use App\Models\TransactionType;
use App\Models\TransportVehicle;
use App\Models\UserTour;
use App\Models\Wallet;
use App\Models\WalletTransactions;
use Helper;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserTourController extends Controller
{
    //

    public function index()
    {
        //
        $tours = UserTour::all();
        foreach ($tours as $tour) {
            $tour->fromCity;
            $tour->toCity;
            $tour->hotel;
            $tour->departureVehicle;
            $tour->arrivalVehicle;
            $tour->agency;
            $tour->user;
            $tour->status;
        }
        return Inertia::render('userTours/ToursList', [
            'tours' => $tours
        ]);
    }

    public function edit($id)
    {
        //
        $tour = UserTour::findOrFail($id);
        $tour->fromCity;
        $tour->toCity;
        $tour->hotel;
        $tour->departureVehicle;
        $tour->arrivalVehicle;
        $tour->agency;
        $tour->user;
        $tour->status;
        $tour->services;
        $tour->rooms = TourRoom::where('tour_id', $tour->id)->get();
        // foreach ($tour->rooms as $room) {
        //     $room->capacity = (int) $room->capacity;
        // }
        $tour->payablePrice = number_format($tour->payablePrice, 0, '.', ',');
        $tour->paied_price = number_format($tour->paied_price, 0, '.', ',');



        //get hotels       
        $hotels = Hotel::where('city_id', $tour->to_city_id)
            ->where('available_time_from', '<=', $tour->departure_date_time)
            ->where('available_time_to', '>=', $tour->arrival_date_time)
            ->orderBy('adult_price', 'asc');
        //get
        $hotels = $hotels->get();



        //filter items that capacity bigger than used_capacity
        $hotels = $hotels->filter(function ($item) use ($tour) {
            return $item->capacity - $item->used_capacity - $tour->adult_count - $tour->kid_count - $tour->teen_count - $tour->infant_count >= 0;
        });


        //get departure vehicles
        $departure_vehicles = TransportVehicle::
            where('from_city', $tour->from_city_id)->
            where('to_city', $tour->to_city_id)->
            where('active', 1);
        $departure_vehicle = TransportVehicle::find($tour->departure_vehicle_id);
        $departure_vehicles = $departure_vehicles->where('transport_type', $departure_vehicle->transport_type)->get();


        $departure_vehicles_array = array();
        foreach ($departure_vehicles as $vehicle) {

            if (Helper::isSameDay($vehicle->departure_date_time / 1000, $tour->departure_date_time / 1000)) {

                //check if vehicle capacity is enough
                if ($vehicle->capacity - $vehicle->used_count - $tour->adult_count - $tour->kid_count - $tour->teen_count - $tour->infant_count >= 0) {
                    //check if hotel is available in $vehicle->arrival_date_time
                    if (
                        $tour->hotel->available_time_from <= $vehicle->arrival_date_time &&
                        $tour->hotel->available_time_to >= $vehicle->arrival_date_time
                    ) {
                        $departure_vehicles_array[] = $vehicle;
                        // break;
                    }
                }
            }
        }

        //get arrival vehicles
        $arrival_vehicles = TransportVehicle::
            where('to_city', $tour->from_city_id)->
            where('from_city', $tour->to_city_id)->
            where('active', 1);
        $arrival_vehicle = TransportVehicle::find($tour->arrival_vehicle_id);
        $arrival_vehicles = $arrival_vehicles->where('transport_type', $arrival_vehicle->transport_type)->get();

        $arrival_vehicles_array = array();
        foreach ($arrival_vehicles as $vehicle) {

            if (Helper::isSameDay($vehicle->departure_date_time / 1000, $tour->arrival_date_time / 1000)) {
                if ($vehicle->capacity - $vehicle->used_count - $tour->adult_count - $tour->kid_count - $tour->teen_count - $tour->infant_count >= 0) {
                    $arrival_vehicles_array[] = $vehicle;
                }
            }
        }

        //services 
        $services = TourService::all();


        return Inertia::render('userTours/EditTour', [
            'tour' => $tour,
            'hotels' => $hotels,
            'departure_vehicles' => $departure_vehicles_array,
            'arrival_vehicles' => $arrival_vehicles_array,
            'services' => $services
        ]);
    }

    public function freeHotels(Request $request)
    {
        //validate request
        $request->validate([
            'from' => 'required',
            'to' => 'required',
            'city' => 'required',
            'adult' => 'required',
            'kids' => 'required',
            'teens' => 'required',
            'infants' => 'required',
        ]);
        //get hotels       
        $hotels = Hotel::where('city_id', $request->city)
            ->where('available_time_from', '<=', $request->from)
            ->where('available_time_to', '>=', $request->to)
            ->get();




        // //filter items that capacity bigger than used_capacity
        // $hotels = $hotels->filter(function ($item) use ($request) {
        //     return $item->capacity - $item->used_capacity - $request->adult - $request->kids - $request->teens - $request->infants >= 0;
        // });

        return response()->json($hotels);
    }

    public function freeDepartureVehicles(Request $request)
    {
        //validate request
        $request->validate([
            'from' => 'required',
            'to' => 'required',
            'from_city' => 'required',
            'to_city' => 'required',
            'type' => 'required',
            'hotel' => 'required',
            'adult' => 'required',
            'kids' => 'required',
            'teens' => 'required',
            'infants' => 'required',
        ]);

        //get hotel
        $hotel = Hotel::findOrFail($request->hotel);

        //get departure vehicles
        $departure_vehicles = TransportVehicle::
            where('from_city', $request->from_city)->
            where('to_city', $request->to_city)->
            where('active', 1);

        $departure_vehicles = $departure_vehicles->where('transport_type', $request->type)->get();


        $departure_vehicles_array = array();
        foreach ($departure_vehicles as $vehicle) {

            if (Helper::isSameDay($vehicle->departure_date_time / 1000, $request->from / 1000)) {

                //check if vehicle capacity is enough
                if ($vehicle->capacity - $vehicle->used_count - $request->adult - $request->teens - $request->kids - $request->infants >= 0) {
                    //check if hotel is available in $vehicle->arrival_date_time
                    if (
                        $hotel->available_time_from <= $vehicle->arrival_date_time &&
                        $hotel->available_time_to >= $vehicle->arrival_date_time
                    ) {
                        $departure_vehicles_array[] = $vehicle;
                        // break;
                    }
                }
            }
        }
        return response()->json($departure_vehicles_array);
    }



    public function freeArrivalVehicles(Request $request)
    {
        //validate request
        $request->validate([
            'from' => 'required',
            'to' => 'required',
            'from_city' => 'required',
            'to_city' => 'required',
            'type' => 'required',
            'hotel' => 'required',
            'adult' => 'required',
            'kids' => 'required',
            'teens' => 'required',
            'infants' => 'required',
        ]);

        //get hotel
        $hotel = Hotel::findOrFail($request->hotel);

        //get arrival vehicles
        $arrival_vehicles = TransportVehicle::
            where('to_city', $request->from_city)->
            where('from_city', $request->to_city)->
            where('active', 1);

        $arrival_vehicles = $arrival_vehicles->where('transport_type', $request->type)->get();

        $arrival_vehicles_array = array();
        foreach ($arrival_vehicles as $vehicle) {

            if (Helper::isSameDay($vehicle->departure_date_time / 1000, $request->to / 1000)) {
                if ($vehicle->capacity - $vehicle->used_count - $request->adult - $request->teens - $request->kids - $request->infants >= 0) {
                    $arrival_vehicles_array[] = $vehicle;
                }
            }
        }

        return response()->json($arrival_vehicles_array);
    }

    public function calculatePrice(Request $request)
    {
        $request->validate([
            'from_date' => 'required',
            'to_date' => 'required',
            'from_city' => 'required',
            'to_city' => 'required',
            'from_vehicle' => 'required',
            'to_vehicle' => 'required',
            'hotel' => 'required',
            'adult' => 'required',
            'kids' => 'required',
            'teens' => 'required',
            'infants' => 'required',
            'fullboard' => 'required',
            // 'services' => 'required',
        ]);

        //calc price
        $calculateable = Helper::calculateTourInfo($request->from_vehicle, $request->to_vehicle, $request->hotel, $request->adult, $request->kids, $request->teens, $request->infants, [], true);
        $payablePrice = $calculateable["payable_price"];

        if ($request->services) {
            //convert $request->setvices to array by ,
            $services = explode(",", $request->services);

            // add services to $calculateable
            foreach ($services as $service) {
                $service = TourService::findOrFail($service);
                $payablePrice += $service["price"] * 1.09;
            }
        }

        $hotel = Hotel::findOrFail($request->hotel);
        //aff if $request->fullboard is 1
        if ($request->fullboard == 1) {
            $payablePrice += $hotel->fullboard_price *
                ($request->adult + $request->kids + $request->teens + $request->infants) * 1.09;
        }

        //add if breakfast is 1
        if ($request->breakfast == 1) {
            $payablePrice += $hotel->free_breakfast_price *
                ($request->adult + $request->kids + $request->teens + $request->infants) * 1.09;
        }
        //add if dinner is 1
        if ($request->dinner == 1) {
            $payablePrice += $hotel->free_dinner_price *
                ($request->adult + $request->kids + $request->teens + $request->infants) * 1.09;
        }
        //add if lunch is 1
        if ($request->lunch == 1) {
            $payablePrice += $hotel->free_lunch_price *
                ($request->adult + $request->kids + $request->teens + $request->infants) * 1.09;
        }



        return [
            // 'hotel' => $item,
            // 'departure_vehicle' => $departure_vehicle,
            // 'arrival_vehicle' => $arrival_vehicle,
            'payable_price_format' => number_format($payablePrice, 0, '.', ','),
            'calculateable' => $calculateable,
            // 'arrival_vehicles' => $arrival_vehicles,
            // 'dep' => $departure_vehicles

        ];

    }

    public function update(Request $request, $id)
    {
        // return $request->all();
        //validate data
        $request->validate([
            'hotel' => 'required',
            'departure_date_time' => 'required',
            'arrival_date_time' => 'required',
            'departure_transport_type' => 'required',
            'arrival_transport_type' => 'required',
            'departure_transport_vehicle' => 'required',
            'arrival_transport_vehicle' => 'required',
            'fullboard' => 'required',
            // 'services' => 'required',
            'rooms' => 'required',
            'prices' => 'required',

        ]);
        $tour = UserTour::findOrFail($id);
        $user = $tour->user;

        $status = "ADMIN_PENDING";
        //compare two items of prices array
        if ($request->prices[0] >= $request->prices[1]) {
            //must return diff to user wallet and make tour status FINAL
            $status = "FINAL";

            //user wallet
            $wallet = Wallet::where('user_id', $user->id)->first();
            $wallet->update([
                'user_id' => $user->id,
                'amount' => $wallet->amount + ($request->prices[0] - $request->prices[1])
            ]);

            WalletTransactions::create([
                'wallet_id' => $wallet->id,
                'amount' => $request->prices[0] - $request->prices[1],
                'type' => TransactionType::where('value', 'RETURN')->first()->id,
                'description' => 'بازگشت مبلغ از سفر ' . $tour->id,
            ]);
        } else {
            //must sms to user to pay diff and make tour status PAY_PENDING
            $status = "PAY_PENDING";
        }
        $status_id = TourStatus::where('value', $status)->first()->id;

        //update usertTour
        $tour->update([
            'hotel_id' => $request->hotel,
            'departure_date_time' => $request->departure_date_time,
            'arrival_date_time' => $request->arrival_date_time,
            'departure_vehicle_id' => $request->departure_transport_vehicle,
            'arrival_vehicle_id' => $request->arrival_transport_vehicle,
            'fullboard' => $request->fullboard,
            'breakfast' => $request->breakfast,
            'lunch' => $request->lunch,
            'dinner' => $request->dinner,
            // 'services' => $request->services,
            // 'rooms' => $request->rooms,
            // 'prices' => $request->prices,
            'payablePrice' => $request->prices[1],
            'status_id' => $status_id,
        ]);

        //update services and rooms
        $tour->services()->sync($request->services);

        //remove all tour rooms
        TourRoom::where('tour_id', $tour->id)->delete();
        //add new tour rooms
        foreach ($request->rooms as $room) {
            TourRoom::create([
                'tour_id' => $tour->id,
                'name' => $room['name'],
                'capacity' => $room['capacity'],
            ]);
        }


        return redirect('/dashboard/userTour')->with('success', 'تور با موفقیت ویرایش شد');


    }

    //fail
    public function fail($id)
    {
        $tour = UserTour::findOrFail($id);
        //update tour status
        $tour->update([
            'status_id' => TourStatus::where('value', 'FAILED')->first()->id,
        ]);
        return redirect('/dashboard/userTour')->with('success', 'تور با موفقیت لغو شد');
    }
}