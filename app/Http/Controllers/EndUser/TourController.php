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
use App\Models\userTourService;
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
                    if ($vehicle->capacity - $vehicle->used_count - $request->adult - $request->kids - $request->teens - $request->infants >= 0) {
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
                    if ($vehicle->capacity - $vehicle->used_count - $request->adult - $request->kids - $request->teens - $request->infants >= 0) {
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
            'order' => 'string|in:date,price',
            'type' => 'string|in:departure,arrival',
            'hotel_id' => 'required|integer',
            'prev_departure_vehicle_id' => 'required|integer',
            'prev_arrival_vehicle_id' => 'required|integer',
        ]);


        $prevDepartureVehicle = TransportVehicle::find($request->prev_departure_vehicle_id);
        //show error if prev vehicle not found
        if (!$prevDepartureVehicle) {
            return response()->json([
                'message' => 'prev vehicle not found'
            ], 404);
        }
        $prevArrivalVehicle = TransportVehicle::find($request->prev_arrival_vehicle_id);
        //show error if prev vehicle not found
        if (!$prevArrivalVehicle) {
            return response()->json([
                'message' => 'prev vehicle not found'
            ], 404);
        }

        $hotel = Hotel::find($request->hotel_id);
        //show error if prev vehicle not found
        if (!$hotel) {
            return response()->json([
                'message' => 'hotel not found'
            ], 404);
        }

        //calculate price of prevDepartureVehicle 
        $prevPrice = Helper::calculateTourInfo($prevDepartureVehicle->id, $prevArrivalVehicle->id, $hotel->id, $request->adult, $request->kids, $request->teens, $request->infants);



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
            // where('transport_type', $request->vehicle_type)->
            where('active', 1)->
            orderBy($order, 'asc')->
            get();

        $vehicles = $vehicles->map(function ($vehicle) use ($request, $prevPrice) {

            //check if vehicle departure date time is same as request from date
            if (Helper::isSameDay($vehicle->departure_date_time / 1000, $request->date / 1000)) {
                //check if vehicle capacity is enough
                if ($vehicle->capacity - $vehicle->used_count - $request->adult - $request->kids - $request->teens - $request->infants >= 0) {
                    //check if hotel is available in $vehicle->arrival_date_time
                    //calculate price of vehicle
                    // $vehicle->total_price = $vehicle->adult_price * $request->adult + $vehicle->kids_price * $request->kids + $vehicle->teens_price * $request->teens + $vehicle->infants_price * $request->infants;
                    $departure_vehicle_id = $request->type == 'departure' ? $vehicle->id : $request->prev_departure_vehicle_id;
                    $arrival_vehicle_id = $request->type == 'arrival' ? $vehicle->id : $request->prev_arrival_vehicle_id;
                    $vehicle->total_price = Helper::calculateTourInfo($departure_vehicle_id, $arrival_vehicle_id, $request->hotel_id, $request->adult, $request->kids, $request->teens, $request->infants);
                    $vehicle->transportCompany->logo;
                    //difference of price between prev vehicle and current vehicle
                    $vehicle->price_difference = $vehicle->total_price - $prevPrice;
                    //return if price difference is not zero
                    // if ($vehicle->price_difference != 0) {
                    return $vehicle;
                    // }


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

    //price calculator
    /**
     * @OA\Post(
     *  path="/v1/tours/calculatePrice",
     * tags={"Tours"},
     * summary="calc price",
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass tour suggest parameters",
     *    @OA\JsonContent(
     *       required={"adult","kids","teens","infants","from_date","to_date","from_city_id","to_city_id","departure_vehicle_type","arrival_vehicle_type"},
     *       @OA\Property(property="adult", type="integer", example="2"),
     *       @OA\Property(property="kids", type="integer", example="2"),
     *       @OA\Property(property="teens", type="integer", example="2"),
     *       @OA\Property(property="infants", type="integer", example="2"),
     *       @OA\Property(property="hotel_id", type="integer", example="2"),
     *       @OA\Property(property="arrival_vehicle_id", type="integer", example="7"),
     *       @OA\Property(property="departure_vehicle_id", type="integer", example="6"),
     *       @OA\Property(property="tour_services", type="string", example="3,4,5,6")
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
    public function calculatePrice(Request $request)
    {
        $request->validate([
            'adult' => 'required',
            'kids' => 'required',
            'teens' => 'required',
            'infants' => 'required',
            'hotel_id' => 'required',
            'arrival_vehicle_id' => 'required',
            'departure_vehicle_id' => 'required',
            // 'tour_services' => 'required',
        ]);

        $calculateable = Helper::calculateTourInfo($request->departure_vehicle_id, $request->arrival_vehicle_id, $request->hotel_id, $request->adult, $request->kids, $request->teens, $request->infants, [], true);

        //return
        return response()->json([
            'calculateable' => $calculateable
        ], 200);

    }

    //save tour
//annotation with these parameters: from_city , to_city , departure_date_time , arrival_date_time , adult_count , teen_count,kid_count,infant_count,hotel_id,departure_vehicle_id,arrival_vehicle_id,fullboard,breakfast,lunch,dinner,services
    /**
     * @OA\Post(
     *  path="/v1/tours/save",
     * tags={"Tours"},
     * summary="save tour",
     * security={{"apiAuth":{}}},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass tour suggest parameters",
     *    @OA\JsonContent(
     *       @OA\Property(property="from_city", type="integer", example="360"),
     *       @OA\Property(property="to_city", type="integer", example="522"),
     *       @OA\Property(property="departure_date_time", type="integer", example="1676881800000"),
     *       @OA\Property(property="arrival_date_time", type="integer", example="1677659400000"),
     *       @OA\Property(property="adult_count", type="integer", example="2"),
     *       @OA\Property(property="teen_count", type="integer", example="2"),
     *       @OA\Property(property="kid_count", type="integer", example="2"),
     *       @OA\Property(property="infant_count", type="integer", example="2"),
     *       @OA\Property(property="hotel_id", type="integer", example="3"),
     *       @OA\Property(property="departure_vehicle_id", type="integer", example="6"),
     *       @OA\Property(property="arrival_vehicle_id", type="integer", example="9"),
     *       @OA\Property(property="fullboard", type="integer", example="1"),
     *       @OA\Property(property="breakfast", type="integer", example="1"),
     *       @OA\Property(property="lunch", type="integer", example="1"),
     *       @OA\Property(property="dinner", type="integer", example="1"),
     *       @OA\Property(property="services", type="string", example="31,32,33,36,38")
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
    public function saveTour(Request $request)
    {


        //validate 
        $request->validate([
            'from_city' => 'required',
            'to_city' => 'required',
            'departure_date_time' => 'required',
            'arrival_date_time' => 'required',
            'adult_count' => 'required',
            'teen_count' => 'required',
            'kid_count' => 'required',
            'infant_count' => 'required',
            'hotel_id' => 'required',
            'departure_vehicle_id' => 'required',
            'arrival_vehicle_id' => 'required',
            'fullboard' => 'required',
            'breakfast' => 'required',
            'lunch' => 'required',
            'dinner' => 'required',
            // 'services' => 'required',
        ]);



        //get user
        $user = Auth::user();

        //search for user tours

        $user_tour = UserTour::where('user_id', $user->id)->where('from_city_id', $request->from_city)->where('to_city_id', $request->to_city)->where('departure_date_time', $request->departure_date_time)->where('arrival_date_time', $request->arrival_date_time)->
            where("status_id", 1)->
            first();

        //prevent to duplicate pending
        if ($user_tour) {
            return response()->json([
                'message' => 'شما یک تور با این مشخصات در حال بررسی دارید'
            ], 400);
        }

        //calculate price
        $calculateable = Helper::calculateTourInfo($request->departure_vehicle_id, $request->arrival_vehicle_id, $request->hotel_id, $request->adult_count, $request->kid_count, $request->teen_count, $request->infant_count, [], true);
        if ($request->services) {
            //convert services to array
            $services = explode(',', $request->services);

            foreach ($services as $service) {
                $service = TourService::find($service);
                $calculateable['payable_price'] += $service->price * 1.09;
            }
        }
        $hotel = Hotel::find($request->hotel_id);
        if ($request->fullboard) {
            $calculateable['payable_price'] += $hotel->fullboard_price * ($request->adult_count + $request->teen_count + $request->kid_count) * 1.09;
        }
        if ($request->breakfast) {
            $calculateable['payable_price'] += $hotel->free_breakfast_price * ($request->adult_count + $request->teen_count + $request->kid_count) * 1.09;
        }
        if ($request->lunch) {
            $calculateable['payable_price'] += $hotel->free_lunch_price * ($request->adult_count + $request->teen_count + $request->kid_count) * 1.09;
        }
        if ($request->dinner) {
            $calculateable['payable_price'] += $hotel->free_dinner_price * ($request->adult_count + $request->teen_count + $request->kid_count) * 1.09;
        }

        //math.ceil payble price
        $calculateable['payable_price'] = ceil($calculateable['payable_price']);

        //save tour
        $tour = new UserTour();
        $tour->user_id = $user->id;
        $tour->from_city_id = $request->from_city;
        $tour->to_city_id = $request->to_city;
        $tour->departure_date_time = $request->departure_date_time;
        $tour->arrival_date_time = $request->arrival_date_time;
        $tour->adult_count = $request->adult_count;
        $tour->teen_count = $request->teen_count;
        $tour->kid_count = $request->kid_count;
        $tour->infant_count = $request->infant_count;
        $tour->hotel_id = $request->hotel_id;
        $tour->departure_vehicle_id = $request->departure_vehicle_id;
        $tour->arrival_vehicle_id = $request->arrival_vehicle_id;
        $tour->fullboard = $request->fullboard;
        $tour->breakfast = $request->breakfast;
        $tour->lunch = $request->lunch;
        $tour->dinner = $request->dinner;
        $tour->status_id = 1;
        $tour->payablePrice = $calculateable['payable_price'];
        $tour->save();

        //save tour services
        if ($request->services) {
            foreach ($services as $service) {
                $tour_service = new userTourService();
                $tour_service->user_tour_id = $tour->id;
                $tour_service->tour_service_id = $service;
                $tour_service->save();
            }
        }

        //minus capacity of hotel and vehicles
        $hotel = Hotel::find($request->hotel_id);
        $hotel->used_capacity += $request->adult_count + $request->teen_count + $request->kid_count + $request->infant_count;
        $hotel->save();

        $departure_vehicle = TransportVehicle::find($request->departure_vehicle_id);
        $departure_vehicle->used_count += $request->adult_count + $request->teen_count + $request->kid_count + $request->infant_count;
        $departure_vehicle->save();

        $arrival_vehicle = TransportVehicle::find($request->arrival_vehicle_id);
        $arrival_vehicle->used_count += $request->adult_count + $request->teen_count + $request->kid_count + $request->infant_count;
        $arrival_vehicle->save();

        return response()->json([
            'calculateable' => $calculateable,
            'tour' => $tour,
        ], 200);
    }

    //view tour
    /**
     * @OA\Get(
     * path="/v1/tours/{id}",
     * summary="View tour",
     * description="View tour",
     * operationId="viewTour",
     * tags={"Tours"},
     * security={ {"apiAuth": {} }},
     * @OA\Parameter(
     *    name="id",
     *    in="path",
     *    description="Tour id",
     *    required=true,
     *    @OA\Schema(
     *       type="integer",
     *       format="int64"
     *    )
     * ),
     * @OA\Response(
     *    response=200,
     *    description="Success",
     *    @OA\MediaType(
     *       mediaType="application/json",
     *    )
     * ),
     * @OA\Response(
     *    response=404,
     *    description="Not found",
     *    @OA\MediaType(
     *       mediaType="application/json",
     *    )
     * ),
     * )
     * 
     */
    public function viewTour($id)
    {
        $tour = UserTour::find($id);
        $tour->fromCity;
        $tour->toCity;
        $tour->departureVehicle;
        $tour->arrivalVehicle;
        $tour->hotel;

        // $tour->hotel->payable_price = $tour->payablePrice;

        //calculate price
        $calculateable = Helper::calculateTourInfo($tour->departure_vehicle_id, $tour->arrival_vehicle_id, $tour->hotel_id, $tour->adult_count, $tour->kid_count, $tour->teen_count, $tour->infant_count, [], true);

        foreach ($tour->services as $service) {
            $calculateable["payable_price"] += $service->price * 1.09;
        }

        //fullboard
        if ($tour->fullboard) {
            $calculateable["payable_price"] += $tour->hotel->fullboard_price * ($tour->adult_count + $tour->teen_count + $tour->kid_count) * 1.09;
        }
        //breakfast
        if ($tour->breakfast) {
            $calculateable["payable_price"] += $tour->hotel->free_breakfast_price * ($tour->adult_count + $tour->teen_count + $tour->kid_count) * 1.09;
        }
        //lunch
        if ($tour->lunch) {
            $calculateable["payable_price"] += $tour->hotel->free_lunch_price * ($tour->adult_count + $tour->teen_count + $tour->kid_count) * 1.09;
        }
        //dinner
        if ($tour->dinner) {
            $calculateable["payable_price"] += $tour->hotel->free_dinner_price * ($tour->adult_count + $tour->teen_count + $tour->kid_count) * 1.09;
        }

        //math.ceil payble price
        $calculateable["payable_price"] = ceil($calculateable["payable_price"]);

        $tour->hotel->payable_price = $calculateable["payable_price"];
        $tour->hotel->days = $calculateable["days"];
        $tour->hotel->nights = $calculateable["nights"];
        $tour->hotel->image;

        if (!$tour) {
            return response()->json([
                'message' => 'تور یافت نشد'
            ], 404);
        }
        return response()->json([
            'tour' => $tour,
            'calculateable' => $calculateable,
        ], 200);
    }
}