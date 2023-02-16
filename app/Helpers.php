<?php

namespace App\Helpers;

use App\Models\Hotel;
use App\Models\ProvinceCity;
use App\Models\TourService;
use App\Models\TransportVehicle;
use GuzzleHttp\Client;

class Helper
{
    public static function sendOtp($code, $mobile)
    {
        $endpoint = env('SMS_URL') . '/' . env('SMS_API_KEY') . '/verify/lookup.json';
        $client = new \GuzzleHttp\Client();
        $receptor = $mobile;
        $token = $code;
        $template = env('SMS_VERIFY_TEMPLATE');

        $response = $client->request('GET', $endpoint, [
            'query' => [
                'receptor' => $receptor,
                'token' => $token,
                'template' => $template,
            ]
        ]);

        // url will be: http://my.domain.com/test.php?key1=5&key2=ABC;

        $statusCode = $response->getStatusCode();
        $content = $response->getBody();
        return $content;

        // or when your server returns json
        // $content = json_decode($response->getBody(), true);
    }
    //check if two unix timestamps in one day or not
    public static function isSameDay($timestamp1, $timestamp2)
    {
        $date1 = date('Y-m-d', $timestamp1);
        $date2 = date('Y-m-d', $timestamp2);
        return $date1 == $date2;
    }

    //get days
    public static function getDays($start, $end)
    {
        $current = strtotime($start);
        $last = strtotime($end);

        $datediff = $last - $current;
        return round($datediff / (60 * 60 * 24));
    }

    public static function calculateTourInfo($departure_vehicle_id, $arrival_vehicle_id, $hotel_id, $adult, $kids, $teens, $infants, $services = [], $full = false)
    {
        $departure_vehicle = TransportVehicle::find($departure_vehicle_id);
        $arrival_vehicle = TransportVehicle::find($arrival_vehicle_id);
        $item = Hotel::find($hotel_id);

        //check if departure vehicle arrival time is bigger than hotel check in time or not
        $item->arrival_date = date('Y-m-d', $departure_vehicle->arrival_date_time / 1000);
        $item->leave_date = date('Y-m-d', $arrival_vehicle->departure_date_time / 1000);

        $item->arrival_time = date('H:i:s', $departure_vehicle->arrival_date_time / 1000);
        $item->leave_time = date('H:i:s', $arrival_vehicle->departure_date_time / 1000);

        $item->days = Helper::getDays($item->arrival_date, $item->leave_date);
        $item->nights = Helper::getDays($item->arrival_date, $item->leave_date);

        if (date('H:i:s', $departure_vehicle->arrival_date_time / 1000) < $item->check_in) {
            $item->payable_early_check_in_price = $item->early_check_in_price * ($adult + $kids + $teens + $infants);
            $item->days = $item->days + 1;
        } else {
            $item->payable_early_check_in_price = 0;
        }

        //check if arrival vehicle departure time is smaller than hotel check out time or not
        if (date('H:i:s', $arrival_vehicle->departure_date_time / 1000) > $item->check_out) {
            $item->payable_late_check_out_price = $item->late_check_out_price * ($adult + $kids + $teens + $infants);
            $item->nights = $item->nights + 1;
        } else {
            $item->payable_late_check_out_price = 0;
        }

        //calculate price of hotel from adult and kids and teens and infants
        $item->hotel_price = $item->adult_price * $adult + $item->kids_price * $kids + $item->teens_price * $teens + $item->infants_price * $infants;

        //calculate price of departure vehicle from adult and kids and teens and infants
        $item->departure_vehicle_price = $departure_vehicle->adult_price * $adult + $departure_vehicle->kids_price * $kids + $departure_vehicle->teens_price * $teens + $departure_vehicle->infants_price * $infants;

        //calculate price of arrival vehicle from adult and kids and teens and infants
        $item->arrival_vehicle_price = $arrival_vehicle->adult_price * $adult + $arrival_vehicle->kids_price * $kids + $arrival_vehicle->teens_price * $teens + $arrival_vehicle->infants_price * $infants;
        //calculate total price
        $item->total_price = $item->hotel_price + $item->departure_vehicle_price + $item->arrival_vehicle_price + $item->payable_early_check_in_price + $item->payable_late_check_out_price;

        //map tour services and sum price
        // foreach ($services as $service) {
        //     $item = TourService::find($service);
        //     $item->total_price = $item->total_price + $item->price;
        // }

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
        if ($full) {
            $from_city = ProvinceCity::find($departure_vehicle->from_city);
            $to_city = ProvinceCity::find($departure_vehicle->to_city);

            $departure_vehicle->transportCompany->logo;
            $arrival_vehicle->transportCompany->logo;

            return [
                'payable_price' => $item->payable_price,
                'from_city' => $from_city,
                'to_city' => $to_city,
                'adult' => $adult,
                'kids' => $kids,
                'teens' => $teens,
                'infants' => $infants,
                'days' => $item->days,
                'nights' => $item->nights,
                'departureVehicle' => $departure_vehicle,
                'arrivalVehicle' => $arrival_vehicle,
                'services' => $services,
                // 'departure_vehicle' => $departure_vehicle,
                // 'arrival_vehicle' => $arrival_vehicle,
                // 'hotel' => $item,
            ];
        } else {
            return $item->payable_price;
        }

    }


}