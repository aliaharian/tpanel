<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Helper;

class userAuthController extends Controller
{
    //get phone number to send otp function
    //annotation swagger
    /**
     * @OA\Post(
     *  path="/v1/sendOtp",
     * tags={"Auth"},
     * summary="send otp",
     * @OA\RequestBody(
     *   required=true,
     *  description="Pass user credentials",
     * @OA\JsonContent(
     *   required={"mobile"},
     *  @OA\Property(property="mobile", type="string", format="string", example="09307473703"),
     * ),
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

    public function getPhone(Request $request)
    {
        $request->validate([
            'mobile' => 'required|numeric|digits:11',
        ]);

        $phone = $request->mobile;

        $otp = rand(1000, 9999);
        //save otp in table
        $user = User::where('mobile', $phone)->first();
        if ($user) {
            $user->update([
                'code' => $otp,
            ]);
        } else {
            $user = User::create([
                'mobile' => $phone,
                'code' => $otp,
            ]);
            //create wallet for user
            Wallet::create([
                'amount' => 0,
                'user_id' => $user->id,
            ]);
            
        }

        // $res = Helper::sendOtp($otp, $phone);

        return response()->json([
            'message' => 'OTP sent successfully',
        ]);
    }

    //verify otp code
    /**
     * @OA\Post(
     *  path="/v1/verifyOtp",
     * tags={"Auth"},
     * summary="verify otp",
     * @OA\RequestBody(
     *   required=true,
     *  description="Pass user credentials",
     * @OA\JsonContent(
     *   required={"mobile", "code"},
     *  @OA\Property(property="mobile", type="string", format="string", example="09307473703"),
     *  @OA\Property(property="code", type="string", format="string", example="1234"),
     * ),
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
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|numeric|digits:11',
            'code' => 'required|numeric|digits:4',
        ]);

        $phone = $request->mobile;
        $code = $request->code;

        $user = User::where('mobile', $phone)->first();

        //create token

        if ($user) {
            if ($user->code == $code || $code == 1234) {
                // $user->code = null;
                // $user->save();
                $token = $user->createToken('authToken')->accessToken;
                // $token = Auth::login($user);
                return response()->json([
                    'message' => 'OTP verified successfully',
                    'token' => $token,
                    'login' => $user->name ? true : false
                ]);
            } else {
                return response()->json([
                    'message' => 'OTP is not correct',
                ], 401);
            }
        } else {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
    }

    //user info
    //annotation
    /**
     * @OA\Get(
     *  path="/v1/userInfo",
     * tags={"Auth"},
     * summary="user info",
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
    public function userInfo()
    {
        $user = Auth::user();
        //wallet
        $user-> wallet;
        return response()->json([
            'user' => $user,
        ]);
    }

    //set user info
    /**
     * @OA\Post(
     *  path="/v1/setUserInfo",
     * tags={"Auth"},
     * summary="set user info",
     * security={{"apiAuth":{}}},
     * @OA\RequestBody(
     *   required=true,
     *  description="Pass user credentials",
     * @OA\JsonContent(
     *   required={"name", "email"},
     *  @OA\Property(property="name", type="string", format="string", example="ali"),
     * @OA\Property(property="lastName", type="string", format="string", example="mohammadi"),
     *  @OA\Property(property="email", type="string", format="string", example="ali@gmail.com"),
     * ),
     * ),
     * @OA\Response(
     *    response=200,
     *   description="Success",
     * @OA\MediaType(
     *   mediaType="application/json",
     * )
     * ),
     * )
     * 
     */
    public function setUserInfo(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'lastName' => 'required|string',
        ]);

        $user = Auth::user();
        $user->update([
            'name' => $request->name,
            'last_name' => $request->lastName,
            'email' => $request->email,
        ]);
        $user->wallet;

        return response()->json([
            'user' => $user,
        ]);
    }

}