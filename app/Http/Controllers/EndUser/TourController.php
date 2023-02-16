<?php

namespace App\Http\Controllers\EndUser;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\HotelService;
use App\Models\ProvinceCity;
use App\Models\RoomService;
use App\Models\TourService;
use App\Models\TransportVehicle;
use App\Models\UserTour;
use Illuminate\Http\Request;
use Mockery\Undefined;
use Illuminate\Support\Facades\Auth;

class TourController extends Controller
{


    //offer hotels list with getting number of adult, kids, teens , infants, from date, to date , from city , to city , departure vehicle type , to vehicle type
    //post method annotation
    /**
     * @OA\Post(
     *  path="/v1/tours/suggest/hotel",
     * tags={"Tours"},
     * summary="suggest hotels",
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass tour suggest parameters",
     *    @OA\JsonContent(
     *       required={"adult","kids","teens","infants","from_date","to_date","from_city_id","to_city_id","departure_vehicle_type","arrival_vehicle_type"},
     *       @OA\Property(property="adult", type="integer", example="2"),
     *       @OA\Property(property="kids", type="integer", example="2"),
     *       @OA\Property(property="teens", type="integer", example="2"),
     *       @OA\Property(property="infants", type="integer", example="2"),
     *       @OA\Property(property="from_date", type="string", example="1675456200000"),
     *       @OA\Property(property="to_date", type="string", example="1676233800000"),
     *       @OA\Property(property="from_city_id", type="integer", example="360"),
     *       @OA\Property(property="to_city_id", type="integer", example="522"),
     *       @OA\Property(property="departure_vehicle_type", type="string", example="TRAIN"),
     *       @OA\Property(property="arrival_vehicle_type", type="string", example="TRAIN"),
     *       @OA\Property(property="order", type="string", example="price"),
     *      @OA\Property(property="min_price", type="integer", example="100000"),
     *     @OA\Property(property="max_price", type="integer", example="100000"),
     *    @OA\Property(property="hotel_rate", type="integer", example="5"),
     *   @OA\Property(property="hotel_stars", type="integer", example="5"),
     *    ),
     * ),
     * @OA\Response(
     *     response=200,
     *    description="Success",
     * @OA\MediaType(
     *     mediaType="application/json",
     * )
     * ),
     * )
     * 
     */
    public function suggestHotel(Request $request)
    {

        //validate request
        $request->validate([
            'adult' => 'required',
            'kids' => 'required',
            'teens' => 'required',
            'infants' => 'required',
            'from_date' => 'required',
            'to_date' => 'required',
            'from_city_id' => 'required',
            'to_city_id' => 'required',
            'departure_vehicle_type' => 'required',
            'arrival_vehicle_type' => 'required',
            'order' => 'string|in:departure_date,arrival_date,price',
            'min_price' => 'numeric',
            'max_price' => 'numeric',
            // 'hotel_rate' => 'numeric',
            // 'hotel_stars' => 'numeric',
        ]);

        //find hotels that from city id and to city id is compatible and available time from and time to in range of from date and to date
        $hotels = Hotel::where('city_id', $request->to_city_id)
            ->where('available_time_from', '<=', $request->from_date)
            ->where('available_time_to', '>=', $request->to_date)
            ->orderBy('adult_price', 'asc');

        //filter hotel rate
        if ($request->has('hotel_rate') && $request->hotel_rate != 'all') {
            //split hotel rate by , and get array
            $hotel_rates = explode(',', $request->hotel_rate);
            $hotels = $hotels->whereIn('rate', $hotel_rates);
        }

        //filter hotel stars
        if ($request->has('hotel_stars') && $request->hotel_stars != 'all') {
            $hotels = $hotels->where('stars', $request->hotel_stars);
        }

        //get
        $hotels = $hotels->get();



        //filter items that capacity bigger than used_capacity
        $hotels = $hotels->filter(function ($item) use ($request) {
            return $item->capacity - $item->used_capacity - $request->adult - $request->kids - $request->teens - $request->infants >= 0;
        });

        //suggest a vehicle for each hotel

        $hotels = $hotels->map(function ($item) use ($request) {
            //suggest a vehicle for each hotel check only day, month and year of from date
            $order = 'adult_price';
            if ($request->order == 'departure_date') {
                $order = 'departure_date_time';
            } else if ($request->order == 'price') {
                $order = 'adult_price';
            } else if ($request->order == 'arrival_date') {
                $order = 'arrival_date_time';
            }

            //add image
            $item->image;

            $departure_vehicles = TransportVehicle::
                where('from_city', $request->from_city_id)->
                where('to_city', $request->to_city_id)->
                where('active', 1)->
                orderBy($order, 'asc');

            if ($request->departure_vehicle_type != 'all') {
                //split departure vehicle type by , and get array
                $departure_vehicle_types = explode(',', $request->departure_vehicle_type);
                $departure_vehicles = $departure_vehicles->whereIn('transport_type', $departure_vehicle_types)->get();
                // $departure_vehicles = $departure_vehicles->where('transport_type', $request->departure_vehicle_type)->get();
            } else {
                $departure_vehicles = $departure_vehicles->get();
            }


            $departure_vehicles_array = array();
            foreach ($departure_vehicles as $vehicle) {

                if (Helper::isSameDay($vehicle->departure_date_time / 1000, $request->from_date / 1000)) {

                    //check if vehicle capacity is enough
                    if ($vehicle->capacity - $vehicle->used_capacity - $request->adult - $request->kids - $request->teens - $request->infants >= 0) {
                        //check if hotel is available in $vehicle->arrival_date_time
                        if (
                            $item->available_time_from <= $vehicle->arrival_date_time &&
                            $item->available_time_to >= $vehicle->arrival_date_time
                        ) {
                            $departure_vehicles_array[] = $vehicle;
                            // break;
                        }
                    }
                }
            }

            $arrival_vehicles = TransportVehicle::
                where('to_city', $request->from_city_id)->
                where('from_city', $request->to_city_id)->
                where('active', 1)->
                orderBy($order, 'asc');

            if ($request->arrival_vehicle_type != 'all') {
                //split arrival vehicle type by , and get array
                $arrival_vehicle_types = explode(',', $request->arrival_vehicle_type);
                $arrival_vehicles = $arrival_vehicles->whereIn('transport_type', $arrival_vehicle_types)->get();

                // $arrival_vehicles = $arrival_vehicles->where('transport_type', $request->arrival_vehicle_type)->get();
            } else {
                $arrival_vehicles = $arrival_vehicles->get();
            }
            $arrival_vehicles_array = array();
            foreach ($arrival_vehicles as $vehicle) {
                // return response()->json([
                //     'date2' => date('Y-m-d', $vehicle->departure_date_time / 1000),
                //     'date' => date('Y-m-d', $request->to_date / 1000),
                // ], 200);
                if (Helper::isSameDay($vehicle->departure_date_time / 1000, $request->to_date / 1000)) {
                    // if (date('Y-m-d', $arrival_time / 1000) == date('Y-m-d', $request->to_date / 1000)) {
                    if ($vehicle->capacity - $vehicle->used_capacity - $request->adult - $request->kids - $request->teens - $request->infants >= 0) {
                        $arrival_vehicles_array[] = $vehicle;
                        // break;
                    }
                }
            }

            $departure_vehicle = null;

            //select item from departure vehicles array that has min adult price and date('H:i:s', $departure_vehicle->arrival_date_time / 1000) > $item->check_in
            if (count($departure_vehicles_array) > 0) {
                $departure_vehicle = $departure_vehicles_array[0];

                if ($request->order == 'price') {
                    if (date('H:i:s', $departure_vehicle->arrival_date_time / 1000) < $item->check_in) {
                        foreach ($departure_vehicles_array as $vehicle) {
                            if (date('H:i:s', $vehicle->arrival_date_time / 1000) >= $item->check_in) {
                                $curr_total_price = $departure_vehicle->adult_price * $request->adult + $departure_vehicle->kids_price * $request->kids + $departure_vehicle->teens_price * $request->teens + $departure_vehicle->infants_price * $request->infants;


                                $new_total_price = $vehicle->adult_price * $request->adult + $vehicle->kids_price * $request->kids + $vehicle->teens_price * $request->teens + $vehicle->infants_price * $request->infants;
                                // return $curr_total_price;

                                if ($new_total_price < $curr_total_price + $item->early_check_in_price) {
                                    $departure_vehicle = $vehicle;
                                }
                            } else {
                                continue;
                            }
                        }
                    }
                }
            }

            $arrival_vehicle = null;
            //select item from arrival vehicles array that has min adult price and date('H:i:s', $arrival_vehicle->departure_date_time / 1000) < $item->check_out
            if (count($arrival_vehicles_array) > 0) {
                $arrival_vehicle = $arrival_vehicles_array[0];
                if ($request->order == 'price') {
                    if (date('H:i:s', $arrival_vehicle->departure_date_time / 1000) > $item->check_out) {
                        foreach ($arrival_vehicles_array as $vehicle) {
                            if (date('H:i:s', $vehicle->departure_date_time / 1000) <= $item->check_out) {
                                $curr_total_price = $arrival_vehicle->adult_price * $request->adult + $arrival_vehicle->kids_price * $request->kids + $arrival_vehicle->teens_price * $request->teens + $arrival_vehicle->infants_price * $request->infants;

                                $new_total_price = $vehicle->adult_price * $request->adult + $vehicle->kids_price * $request->kids + $vehicle->teens_price * $request->teens + $vehicle->infants_price * $request->infants;

                                if ($new_total_price < $curr_total_price + $item->late_check_out_price) {
                                    $departure_vehicle = $vehicle;
                                }
                            } else {
                                continue;
                            }
                        }
                    }
                }
            }


            //if there is a vehicle for this hotel return hotel with vehicle
            if ($departure_vehicle && $arrival_vehicle) {
                //check if departure vehicle arrival time is bigger than hotel check in time or not
                $item->arrival_date = date('Y-m-d', $departure_vehicle->arrival_date_time / 1000);
                $item->leave_date = date('Y-m-d', $arrival_vehicle->departure_date_time / 1000);

                $item->arrival_time = date('H:i:s', $departure_vehicle->arrival_date_time / 1000);
                $item->leave_time = date('H:i:s', $arrival_vehicle->departure_date_time / 1000);

                $item->days = Helper::getDays($item->arrival_date, $item->leave_date);
                $item->nights = Helper::getDays($item->arrival_date, $item->leave_date);

                if (date('H:i:s', $departure_vehicle->arrival_date_time / 1000) < $item->check_in) {
                    $item->payable_early_check_in_price = $item->early_check_in_price * ($request->adult + $request->kids + $request->teens + $request->infants);
                    $item->days = $item->days + 1;
                } else {
                    $item->payable_early_check_in_price = 0;
                }

                //check if arrival vehicle departure time is smaller than hotel check out time or not
                if (date('H:i:s', $arrival_vehicle->departure_date_time / 1000) > $item->check_out) {
                    $item->payable_late_check_out_price = $item->late_check_out_price * ($request->adult + $request->kids + $request->teens + $request->infants);
                    $item->nights = $item->nights + 1;
                } else {
                    $item->payable_late_check_out_price = 0;
                }

                //calculate price of hotel from adult and kids and teens and infants
                $item->hotel_price = $item->adult_price * $request->adult + $item->kids_price * $request->kids + $item->teens_price * $request->teens + $item->infants_price * $request->infants;

                //calculate price of departure vehicle from adult and kids and teens and infants
                $item->departure_vehicle_price = $departure_vehicle->adult_price * $request->adult + $departure_vehicle->kids_price * $request->kids + $departure_vehicle->teens_price * $request->teens + $departure_vehicle->infants_price * $request->infants;

                //calculate price of arrival vehicle from adult and kids and teens and infants
                $item->arrival_vehicle_price = $arrival_vehicle->adult_price * $request->adult + $arrival_vehicle->kids_price * $request->kids + $arrival_vehicle->teens_price * $request->teens + $arrival_vehicle->infants_price * $request->infants;
                //calculate total price
                $item->total_price = $item->hotel_price + $item->departure_vehicle_price + $item->arrival_vehicle_price + $item->payable_early_check_in_price + $item->payable_late_check_out_price;
                //add 9 percent tax and calculate payable price with number format
                $item->payable_price = floor($item->total_price + $item->total_price * 9 / 100);

                //hotel services
                $item->hotel_services = $item->hotelServices;
                //room services
                $item->room_services = $item->roomServices;

                //create item from hotel and vehicle prices number format
                // $item->hotel_price = number_format($item->hotel_price);
                // $item->departure_vehicle_price = number_format($item->departure_vehicle_price);
                // $item->arrival_vehicle_price = number_format($item->arrival_vehicle_price);
                // $item->total_price = number_format($item->total_price);
                if ($request->min_price) {
                    if ($item->payable_price < $request->min_price) {
                        return null;
                    }
                }
                if ($request->max_price) {
                    if ($item->payable_price > $request->max_price) {
                        return null;
                    }
                }

                return [
                    'hotel' => $item,
                    'departure_vehicle' => $departure_vehicle,
                    'arrival_vehicle' => $arrival_vehicle,
                    // 'arrival_vehicles' => $arrival_vehicles,
                    // 'dep' => $departure_vehicles

                ];
            } else {
                return null;
            }

        });


        //fromcity and tocity
        $from_city = ProvinceCity::find($request->from_city_id);
        $to_city = ProvinceCity::find($request->to_city_id);
        //return json

        return response()->json([
            //return hotels remove null
            'hotels' => $hotels->filter(function ($item) {
                return $item != null;
            }),
            'from_date' => date('Y-m-d', $request->from_date / 1000),
            'to_date' => date('Y-m-d', $request->to_date / 1000),
            'from_city' => $from_city,
            'to_city' => $to_city,
            'time2' => date('Y-m-d', (1676917800)),
            'time' => date('Y-m-d', (1677616200))
        ], 200);
    }


    //offer vehicles list with getting number of adult, kids, teens , infants, from date, to date , from city , to city , departure vehicle type , to vehicle type
    //post method annotation
    /**
     * @OA\Post(
     *  path="/v1/tours/suggest/vehicle",
     * tags={"Tours"},
     * summary="suggest vehicles",
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass tour suggest parameters",
     *    @OA\JsonContent(
     *       required={"adult","kids","teens","infants","from_date","to_date","from_city_id","to_city_id","departure_vehicle_type","arrival_vehicle_type"},
     *       @OA\Property(property="adult", type="integer", example="2"),
     *       @OA\Property(property="kids", type="integer", example="2"),
     *       @OA\Property(property="teens", type="integer", example="2"),
     *       @OA\Property(property="infants", type="integer", example="2"),
     *       @OA\Property(property="date", type="string", example="1675456200000"),
     *       @OA\Property(property="from_city_id", type="integer", example="360"),
     *       @OA\Property(property="to_city_id", type="integer", example="522"),
     *       @OA\Property(property="vehicle_type", type="string", example="TRAIN"),
     *       @OA\Property(property="order", type="string", example="price"),
     *       @OA\Property(property="prev_vehicle_id", type="integer", example="2"),
     *    ),
     * ),
     * @OA\Response(
     *     response=200,
     *    description="Success",
     * @OA\MediaType(
     *     mediaType="application/json",
     * )
     * ),
     * )
     * 
     */
    public function suggestVehicle(Request $request)
    {
        //validate request
        $request->validate([
            'adult' => 'required|integer',
            'kids' => 'required|integer',
            'teens' => 'required|integer',
            'infants' => 'required|integer',
            'date' => 'required|integer',
            'from_city_id' => 'required|integer',
            'to_city_id' => 'required|integer',
            'vehicle_type' => 'required|string',
            //validate order from three items: date , price
            'order' => 'string|in:date,price',
            //prev vehicle id in vehicles table
            'prev_vehicle_id' => 'integer',
        ]);

        $prevVehicle = null;
        if ($request->prev_vehicle_id) {
            $prevVehicle = TransportVehicle::find($request->prev_vehicle_id);
            //show error if prev vehicle not found
            if (!$prevVehicle) {
                return response()->json([
                    'message' => 'prev vehicle not found'
                ], 404);
            }

            //calculate price of prevVehicle 
            $prevVehicle->total_price = $prevVehicle->adult_price * $request->adult + $prevVehicle->kids_price * $request->kids + $prevVehicle->teens_price * $request->teens + $prevVehicle->infants_price * $request->infants;
        }
        $order = 'adult_price';
        if ($request->order == 'departure_date') {
            $order = 'departure_date_time';
        } else if ($request->order == 'price') {
            $order = 'adult_price';
        } else if ($request->order == 'arrival_date') {
            $order = 'arrival_date_time';
        }

        $vehicles = TransportVehicle::
            where('from_city', $request->from_city_id)->
            where('to_city', $request->to_city_id)->
            where('transport_type', $request->vehicle_type)->
            where('active', 1)->
            orderBy($order, 'asc')->
            get();

        $vehicles = $vehicles->map(function ($vehicle) use ($request, $prevVehicle) {
            //check only date of departure date time
            // return response()->json([
            //     'date2' => date('Y-m-d', $vehicle->departure_date_time/1000),
            //     'date' => date('Y-m-d', $request->to_date / 1000),
            // ], 200);

            //check if vehicle departure date time is same as request from date
            if (date('Y-m-d', $vehicle->departure_date_time / 1000) == date('Y-m-d', $request->date / 1000)) {

                //check if vehicle capacity is enough
                if ($vehicle->capacity - $vehicle->used_capacity - $request->adult - $request->kids - $request->teens - $request->infants >= 0) {
                    //check if hotel is available in $vehicle->arrival_date_time
                    //calculate price of vehicle
                    $vehicle->total_price = $vehicle->adult_price * $request->adult + $vehicle->kids_price * $request->kids + $vehicle->teens_price * $request->teens + $vehicle->infants_price * $request->infants;
                    //difference of price between prev vehicle and current vehicle
                    if ($prevVehicle) {
                        $vehicle->price_difference = $vehicle->total_price - $prevVehicle->total_price;
                        //return if price difference is not zero
                        if ($vehicle->price_difference != 0) {
                            return $vehicle;
                        }
                    } else {
                        return $vehicle;
                    }

                }
            }
        });


        //return json

        return response()->json([

            //remove null values from array
            'vehicles' => $vehicles->filter(function ($value, $key) {
                return $value != null;
            })->values(),
        ], 200);
    }
    //tour services function

    //annotation
    /**
     * @OA\Get(
     *  path="/v1/tours/services",
     * tags={"Tours"},
     * summary="get tour services",
     * @OA\Response(
     *     response=200,
     *    description="Success",
     * @OA\MediaType(
     *     mediaType="application/json",
     * )
     * ),
     * )
     * 
     */
    public function tourServices()
    {
        $services = TourService::all();
        foreach ($services as $service) {
            //calculate payable price by adding 9 percent to price and parse int price
            $service->payable_price = (int) ($service->price) + $service->price * 0.09;
        }
        return response()->json([
            'services' => $services
        ], 200);
    }

    //user tours list
    /**
     * @OA\Get(
     *  path="/v1/tours/user",
     * tags={"Tours"},
     * summary="get user tours",
     * security={{"apiAuth":{}}},
     * @OA\Response(
     *     response=200,
     *    description="Success",
     * @OA\MediaType(
     *     mediaType="application/json",
     * )
     * ),
     * )
     * 
     */
    public function userTours()
    {
        $user = Auth::user();
        $tours = UserTour::where('user_id', $user->id)->get();
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
        return response()->json([
            'tours' => $tours
        ], 200);
    }

}