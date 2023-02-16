<?php

namespace App\Http\Controllers\EndUser;

use App\Http\Controllers\Controller;
use App\Models\AvailableCity;
use Illuminate\Http\Request;

class CityController extends Controller
{
    //
    //show available cities
    //annotations
    //swager annotation get
    /**
     * @OA\Get(
     *  path="/v1/cities/available",
     * tags={"Cities"},
     * summary="available cities list",
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
    public function available()
    {
        $cities = AvailableCity::all();

        foreach ($cities as $city) {
            $city->city;
        }
        //return json
        return response()->json($cities, 200);
    }
}