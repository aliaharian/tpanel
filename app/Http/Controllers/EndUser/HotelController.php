<?php

namespace App\Http\Controllers\EndUser;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    //hotels list with annotation
    /**
     * @OA\Get(
     *  path="/v1/hotels",
     * tags={"Hotels"},
     * summary="hotels list",
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
    public function index()
    {
        //list of hotels from Hotel model
        $hotels = Hotel::orderBy('id', 'desc')->get();
        //return json

        return response()->json([
            'hotels' => $hotels,
        ], 200);

    }


  





}