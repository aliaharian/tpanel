<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class OtpController extends Controller
{
    //
    public function LoginOtp()
    {
        return Inertia::render('Auth/LoginOtp');
    }
    public function doLoginOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|exists:users,mobile',
        ]);
        //send sms
        $userId = User::where('mobile', $request->mobile)->first()->id;
        $code = rand(pow(10, 4 - 1), pow(10, 4) - 1);
        $user = User::UpdateOrCreate(['id' => $userId], [
            'code' => $code
        ]);
        $endpoint = env('SMS_URL') . '/' . env('SMS_API_KEY') . '/verify/lookup.json';
        $client = new \GuzzleHttp\Client();
        $receptor = $request->mobile;
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

        // or when your server returns json
        // $content = json_decode($response->getBody(), true);


        return response()->json($request->mobile);
        // return Inertia::render('Auth/LoginOtp', [
        //     'currobile' => $request->mobile
        // ]);
    }
    public function confirmOtp(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string|exists:users,mobile',
            'code' => 'required|string|exists:users,code',
        ]);
        $user = User::where('mobile', $request->mobile)->first();

        if ($user->code == $request->code) {
            if (Auth::loginUsingId($user->id, true)) {
                // Authentication passed...
                return redirect('/agencyDashboard');
            }
        } else {
            return response()->json(["error" => "کد تایید صحیح نیست"]);
        }
    }
}