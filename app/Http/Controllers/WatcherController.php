<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\PrintFactor;
use App\Models\ProvinceCity;
use App\Models\TourService;
use App\Models\Watcher;
use App\Models\WatcherTourService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class WatcherController extends Controller
{
    function convert($string)
    {
        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

        $num = range(0, 9);
        $convertedPersianNums = str_replace($persian, $num, $string);
        $englishNumbersOnly = str_replace($arabic, $num, $convertedPersianNums);
        echo $englishNumbersOnly;
        return $englishNumbersOnly;
    }
    public function index()
    {
        $watchers = Watcher::orderBy('id', 'desc')->get();
        foreach ($watchers as $watcher) {
            $watcher->from_city;
            $watcher->to_city;
            $watcher->departure_vehicle;
            $watcher->arrival_vehicle;
        }
        return Inertia::render('watchers/WatchersList', [
            'watchers' => $watchers
        ]);
    }


    public function create()
    {
        //
        $services = TourService::orderBy('id', 'desc')->get();
        $agencies = Agency::all();
        $provinces = ProvinceCity::where('parent', 0)->orderBy('title','ASC')->get();
        return Inertia::render('watchers/CreateWatcher', [
            'services' => $services,
            'agencies' => $agencies,
            'provinces' => $provinces
        ]);
    }


    public function store(Request $request)
    {

        $request->validate([
            'buyer_name' => 'required|string|max:255',
            'national_code' => "required|max:10",
            'mobile' => 'required|max:11',
            'people_count' => 'required|max:3',
            'departure_transport_type' => 'in:BUS,AIRPLANE,TRAIN',
            'departure_transport_vehicle' => 'required',
            // 'departure_transport_name' => 'required|string|max:255',
            // 'departure_date' => 'required|max:255',
            // 'departure_time' => 'required',
            'arrival_transport_type' => 'in:BUS,AIRPLANE,TRAIN',
            'arrival_transport_vehicle' => 'required',
            // 'arrival_transport_name' => 'required|string|max:255',
            // 'arrival_date' => 'required|max:255',
            // 'arrival_time' => 'required',
            'fromCity' => 'required',
            'toCity' => 'required',
            'hotel_name' => 'required|string|max:255',
            'rooms_count' => 'required|max:3',
            // 'room_type' => 'required|string|max:255',
            'stay_length' => 'required|max:3',
            'pricePerPerson' => 'required|max:255',
            'payablePrice' => 'required|max:255',
            // 'markup'=> 'required|max:255',
        ]);
        $departure_transport_logo = null;
        if ($request->departure_transport_logo) {
            $departure_transport_logo = Storage::disk('public')->put('transport_logo', $request->departure_transport_logo);
        }
        $arrival_transport_logo = null;
        if ($request->arrival_transport_logo) {
            $arrival_transport_logo = Storage::disk('public')->put('transport_logo', $request->departure_transport_logo);
        }

        $agency = Agency::find($request->agency_name);
        // return $departure_transport_logo;
        $watcher = Watcher::create([
            'agency_id' => $request->agency_name,
            'is_haghighi' => $request->isHaghighi === 'true' ? true : false,
            'buyer_name' => $request->buyer_name,
            'buyer_national_code' => $request->national_code,
            'fullboard' => $request->fullboard === 'true' ? true : false,
            'breakfast' => $request->fullboard === 'false' ? true : false,
            'mobile_phone' => $request->mobile,
            'people_count' => $request->people_count,
            'departure_transport_type' => $request->departure_transport_type,
            'departure_transport_name' => $request->departure_transport_name,
            'departure_transport_logo' => $departure_transport_logo,
            // 'departure_date' => gmdate("Y-m-d", $request->departure_date),
            'departure_date' => $request->departure_date,
            'departure_time' => $request->departure_time,
            'arrival_transport_type' => $request->arrival_transport_type,
            'arrival_transport_name' => $request->arrival_transport_name,
            'arrival_transport_logo' => $arrival_transport_logo,
            // 'arrival_date' => gmdate("Y-m-d", $request->arrival_date),
            'arrival_date' => $request->arrival_date,
            'arrival_time' => $request->arrival_time,
            'hotel_name' => $request->hotel_name,
            'room_numbers' => $request->rooms_count,
            'room_type' => $request->room_type,
            'stay_length' => $request->stay_length,
            'from_city_id' => $request->fromCity,
            'to_city_id' => $request->toCity,
            // 'services' => [],
            'price_per_adult' => str_replace(",", "", $request->pricePerPerson),
            'total_price' => str_replace(",", "", $request->payablePrice),
            'markup' => $request->agency_name ? $agency->agency_markup_percent : 0,
            'departure_vehicle_id' => $request->departure_transport_vehicle,
            'arrival_vehicle_id' => $request->arrival_transport_vehicle,
        ]);
        if ($request->services) {
            foreach ($request->services as $service) {
                $service = WatcherTourService::Create([
                    'tour_service_id' => $service,
                    'watcher_id' => $watcher->id
                ]);
            }
        }

        return Redirect::route('watchers.index')->with('success', 'واچر با موفقیت ایجاد شد');


    }

    public function show($id)
    {
        $watcher = Watcher::findOrFail($id);

        $watcher->from_city;
        $watcher->to_city;
        $watcher->agency;
        $watcher->departure_vehicle->transportCompany->logo;
        $watcher->arrival_vehicle->transportCompany->logo;
        if ($watcher->agency) {
            $watcher->agency->logo;
        }
        $watcher->services;

        return Inertia::render('watchers/PrintWatcher', [
            'watcher' => $watcher,
            'pricePerAdult' => number_format($watcher->price_per_adult),
            'totalPrice' => number_format($watcher->total_price),
            'agencyPrice' => number_format($watcher->agency_price),
            'userPrice' => number_format($watcher->user_price),
            'admin' => true,
            'agency' => false,
            'baseUrl' => env('APP_URL'),
            'showLogo' => $watcher->show_logo


        ]);
    }

    public function showUserWatcher($hash)
    {
        $watcher_id = PrintFactor::where('url_hash', $hash)->first()->watcher_id;
        $watcher = Watcher::findOrFail($watcher_id);

        $watcher->from_city;
        $watcher->to_city;
        $watcher->agency;
        $watcher->services;
        $watcher->departure_vehicle->transportCompany->logo;
        $watcher->arrival_vehicle->transportCompany->logo;
        if ($watcher->agency) {
            $watcher->agency->logo;
        }

        return Inertia::render('watchers/PrintWatcher', [
            'watcher' => $watcher,
            'pricePerAdult' => number_format($watcher->price_per_adult),
            'totalPrice' => number_format($watcher->user_price),
            'admin' => false,
            'agency' => false,
            'baseUrl' => env('APP_URL'),
            'showLogo' => $watcher->show_logo
        ]);
    }

    public function edit($id)
    {
        $watcher = Watcher::findOrFail($id);

        $services = TourService::orderBy('id', 'desc')->get();
        $agencies = Agency::all();
        $provinces = ProvinceCity::where('parent', 0)->orderBy('title','ASC')->get();
        $watcher->services;
        $watcher->from_city;
        $watcher->to_city;
        return Inertia::render('watchers/CreateWatcher', [
            'watcher' => $watcher,
            'services' => $services,
            'agencies' => $agencies,
            'provinces' => $provinces
        ]);
    }


    public function update(Request $request, $id)
    {

        $request->validate([
            'buyer_name' => 'required|string|max:255',
            'national_code' => "required|max:10",
            'mobile' => 'required|max:11',
            'people_count' => 'required|max:3',
            'departure_transport_type' => 'in:BUS,AIRPLANE,TRAIN',
            'departure_transport_vehicle' => 'required',
            // 'departure_transport_name' => 'required|string|max:255',
            // 'departure_date' => 'required|max:255',
            // 'departure_time' => 'required',
            'arrival_transport_type' => 'in:BUS,AIRPLANE,TRAIN',
            'arrival_transport_vehicle' => 'required',
            // 'arrival_transport_name' => 'required|string|max:255',
            // 'arrival_date' => 'required|max:255',
            // 'arrival_time' => 'required',
            'fromCity' => 'required',
            'toCity' => 'required',
            'hotel_name' => 'required|string|max:255',
            'rooms_count' => 'required|max:3',
            // 'room_type' => 'required|string|max:255',
            'stay_length' => 'required|max:3',
            'pricePerPerson' => 'required|max:255',
            'payablePrice' => 'required|max:255',
        ]);

        $prev = Watcher::findOrFail($id);
        $departure_transport_logo = $prev->departure_transport_logo;
        if ($request->departure_transport_logo) {
            $departure_transport_logo = Storage::disk('public')->put('transport_logo', $request->departure_transport_logo);
        }
        $arrival_transport_logo = $prev->arrival_transport_logo;
        if ($request->arrival_transport_logo) {
            $arrival_transport_logo = Storage::disk('public')->put('transport_logo', $request->departure_transport_logo);
        }
        $agency = Agency::find($request->agency_name);
        // return $departure_transport_logo;
        $watcher = Watcher::updateOrCreate(['id' => $prev->id], [
            'agency_id' => $request->agency_name,
            'is_haghighi' => $request->isHaghighi === 'true' ? true : false,
            'buyer_name' => $request->buyer_name,
            'buyer_national_code' => $request->national_code,
            'fullboard' => $request->fullboard === 'true' ? true : false,
            'breakfast' => $request->fullboard === 'false' ? true : false,
            'mobile_phone' => $request->mobile,
            'people_count' => $request->people_count,
            'departure_transport_type' => $request->departure_transport_type,
            'departure_transport_name' => $request->departure_transport_name,
            'departure_transport_logo' => $departure_transport_logo,
            'departure_date' => $request->departure_date,
            // 'departure_date' => gmdate("Y-m-d", $this->convert($request->departure_date)),
            'departure_time' => $request->departure_time,
            'arrival_transport_type' => $request->arrival_transport_type,
            'arrival_transport_name' => $request->arrival_transport_name,
            'arrival_transport_logo' => $arrival_transport_logo,
            // 'arrival_date' => gmdate("Y-m-d", $this->convert($request->arrival_date)),
            'arrival_date' => $request->arrival_date,
            'arrival_time' => $request->arrival_time,
            'hotel_name' => $request->hotel_name,
            'room_numbers' => $request->rooms_count,
            'room_type' => $request->room_type,
            'stay_length' => $request->stay_length,
            'from_city_id' => $request->fromCity,
            'to_city_id' => $request->toCity,
            // 'services' => [],
            'price_per_adult' => str_replace(",", "", $request->pricePerPerson),
            'total_price' => str_replace(",", "", $request->payablePrice),
            'markup' => $request->agency_name ? $agency->agency_markup_percent : 0,
            'departure_vehicle_id' => $request->departure_transport_vehicle,
            'arrival_vehicle_id' => $request->arrival_transport_vehicle,

        ]);
        $watcher->services;
        // foreach ($watcher->services as $item) {
        WatcherTourService::where('watcher_id', $watcher->id)->delete();
        // }

        foreach ($request->services as $service) {
            $service = WatcherTourService::Create([
                'tour_service_id' => $service,
                'watcher_id' => $watcher->id
            ]);
        }

        return Redirect::route('watchers.index')->with('success', 'واچر با موفقیت ایجاد شد');
    }

    public function destroy($id)
    {
        $service = Watcher::destroy($id);
        return Redirect::route('watchers.index')->with('success', 'واچر با موفقیت حذف شد');

        //
    }

    public function createLink(Request $request)
    {

        $random_base64 = base64_encode(random_bytes(18));

        $tmp = PrintFactor::where('watcher_id', $request->watcher)->first();
        if (!$tmp) {
            $factor = PrintFactor::create([
                'url_hash' => str_replace('+', '', str_replace('/', '', $random_base64)),
                'watcher_id' => $request->watcher
            ]);
        } else {
            $factor = $tmp;
        }

        return response()->json($factor);

    }

    //send sms function 
    public function sendWatcherLink(Request $request)
    {
        $request->validate([
            'mobile' => 'required|string',
            'name' => 'required|string',
            'link' => 'required|string',
        ]);


        $endpoint = env('SMS_URL') . '/' . env('SMS_API_KEY') . '/verify/lookup.json';
        $client = new \GuzzleHttp\Client();
        $receptor = $request->mobile;
        $token2 = $request->link;
        $token = str_replace(' ', "_", $request->name);
        $template = env('SMS_WATCHER_TEMPLATE');

        $response = $client->request('GET', $endpoint, [
            'query' => [
                'receptor' => $receptor,
                'token' => $token,
                'token2' => $token2,
                'template' => $template,
            ]
        ]);

        // url will be: http://my.domain.com/test.php?key1=5&key2=ABC;

        $statusCode = $response->getStatusCode();
        $content = $response->getBody();

        // or when your server returns json
        // $content = json_decode($response->getBody(), true);


        return response()->json($request->link);
    }

    //saveWatcherMarkup
    public function saveWatcherMarkup(Request $request)
    {
        $request->validate([
            'watcher_id' => 'required|exists:watchers,id',
            'markup' => 'required|numeric',
        ]);

        $watcher = Watcher::find($request->watcher_id);
        $watcher->markup = $request->markup;
        $watcher->save();

        return response()->json($watcher);
    }
}