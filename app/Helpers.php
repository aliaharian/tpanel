<?php

namespace App\Helpers;

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


}