<?php

namespace App\Http\Controllers\EndUser;

use App\Http\Controllers\Controller;
use App\Models\Passenger;
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


}