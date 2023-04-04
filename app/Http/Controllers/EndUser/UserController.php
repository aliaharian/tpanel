<?php

namespace App\Http\Controllers\EndUser;

use App\Http\Controllers\Controller;
use App\Models\Passenger;
use Auth;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //user passengers list
    //annotation
    /**
     * @OA\Get(
     *     path="/v1/user/passengers",
     *     summary="user passengers list",
     *     tags={"user"},
     *     security={{"apiAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="user passengers list",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="passengers list"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="This action is unauthorized."
     *             )
     *         )
     *     )
     * )
     */
    public function passengers()
    {
        $user = auth()->user();
        $passengers = Passenger::where('user_id', $user->id)->get();
        return response()->json([
            'data' => $passengers
        ]);
    }

    //user transactions 
    //annotation
    /**
     * @OA\Get(
     *     path="/v1/user/transactions",
     *     summary="user transactions list",
     *     tags={"user"},
     *     security={{"apiAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="user transactions list",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="transactions list"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Unauthenticated."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="This action is unauthorized."
     *             )
     *         )
     *     )
     * )
     */

    public function transactions()
    {
        $user = auth()->user();
        $transactions = $user->wallet->transactions;
        foreach ($transactions as $transaction) {
            $transaction->transactionType = $transaction->transactionType;
            $transaction->dateTime = strtotime($transaction->created_at);
        }
        return response()->json([
            'data' => $transactions
        ]);
    }


    //save passenger
    //annotation
    /**
     * @OA\Post(
     *  path="/v1/user/savePassenger",
     * tags={"user"},
     * summary="save Passenger",
     * security={{"apiAuth":{}}},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass tour suggest parameters",
     *    @OA\JsonContent(
     *       @OA\Property(property="passengers", type="string", example="ali"),
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
    public function savePassenger(Request $request)
    {
        //user
        $user = auth()->user();

        $passenger = (object) $request->passenger;
        $passenger = Passenger::updateOrCreate(
            [
                'national_code' => $passenger->nationalCode,
                'user_id' => $user->id
            ],
            [
                'name' => $passenger->name,
                'last_name' => $passenger->lastName,
                'phone' => $passenger->mobile,
                'male' => $passenger->gender == 'male' ? 1 : 0,
                'day' => $passenger->birthDate[0],
                'month' => $passenger->birthDate[1],
                'year' => $passenger->birthDate[2],
            ]
        );
        return response()->json([
            'message' => 'با موفقیت ثبت شد',
        ], 200);

    }

}