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

    //show hotel with annotation
    /**
     * @OA\Get(
     *  path="/v1/hotels/{id}",
     * tags={"Hotels"},
     * summary="show hotel",
     * @OA\Parameter(
     *     name="id",
     *     in="path",
     *     description="id of hotel",
     *     required=true,
     *     @OA\Schema(
     *         type="integer"
     *     )
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
    public function show($id)
    {
        //show hotel from Hotel model
        $hotel = Hotel::find($id);
        $hotel->cityPlace;
        $hotel->hotelImages;
        $hotel->roomServices;
        $hotel->hotelServices;
        $hotel->image;
        //return json
        return response()->json([
            'hotel' => $hotel,
        ], 200);
    }

}