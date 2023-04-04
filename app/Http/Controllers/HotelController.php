<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\Hotel;
use App\Models\HotelGallery;
use App\Models\HotelHotelService;
use App\Models\HotelRoomService;
use App\Models\HotelService;
use App\Models\ProvinceCity;
use App\Models\RoomService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Redirect;

class HotelController extends Controller
{

    public function index()
    {

        $hotels = Hotel::all();
        foreach ($hotels as $hotel) {

            $hotel->cityPlace;
            $hotel->image;



        }
        return Inertia::render('hotels/Index', [
            'hotels' => $hotels
        ]);

        //
    }


    public function create()
    {
        //
        $provinces = ProvinceCity::where('parent', 0)->orderBy('title','ASC')->get();
        $hotelServices = HotelService::all();
        $roomServices = RoomService::all();
        return Inertia::render('hotels/Create', [
            "provinces" => $provinces,
            "hotelServices" => $hotelServices,
            "roomServices" => $roomServices
        ]);
    }


    public function store(Request $request)
    {
        //


        $request->validate([
            'name' => 'required',
            'stars' => 'required',
            'type' => 'required',
            'address' => 'required',
            'city_id' => 'required',
            'province_id' => 'required',
            'latitude' => 'required',
            // 'longitude' => 'required',
            'description' => 'required',
            'check_in' => 'required',
            'check_out' => 'required',
            'image' => 'required',
            'notes' => 'required',
            'capacity' => 'required',
            'available_time_from' => 'required',
            'available_time_to' => 'required',
            'adultPrice' => 'required',
            'teenPrice' => 'required',
            'kidPrice' => 'required',
            'infantPrice' => 'required',
            'fullboardPrice' => 'required',
            'earlyCheckInPrice' => 'required',
            'lateCheckOutPrice' => 'required',
            'breakfastPrice' => 'required',
            'lunchPrice' => 'required',
            'dinnerPrice' => 'required',
            'rate'=> 'required',

        ]);
        // dd($request->all());  
        $image = null;

        if ($request->image) {
            $md5Name = md5_file($request->file('image')->getRealPath());
            $guessExtension = $request->file('image')->guessExtension();
            $logoFile = $request->file('image')->storeAs('/hotel', $md5Name . '.' . $guessExtension, 'public');

            $image = File::create([
                'name' => $md5Name,
                'path' => '/uploads/hotel',
                'mimeType' => $guessExtension,
            ]);
        }
        // return array_values($request->latitude)[0];
        // dd(array_values($request->latitude)[1]);
        $lat = array_values($request->latitude)[1];
        $long = array_values($request->latitude)[0];
        $hotel = Hotel::create([
            'name' => $request->name,
            'stars' => $request->stars,
            'type' => $request->type,
            'address' => $request->address,
            'city_id' => $request->city_id,
            'province_id' => $request->province_id,
            'latitude' => $lat,
            'longitude' => $long,
            'description' => $request->description,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'image_id' => $image ? $image->id : $image,
            'notes' => $request->notes,
            'capacity' => $request->capacity,
            'available_time_from' => $request->available_time_from,
            'available_time_to' => $request->available_time_to,
            'adult_price' => str_replace(",", "", $request->adultPrice),
            'teen_price' => str_replace(",", "", $request->teenPrice),
            'kid_price' => str_replace(",", "", $request->kidPrice),
            'infant_price' => str_replace(",", "", $request->infantPrice),
            'fullboard_price' => str_replace(",", "", $request->fullboardPrice),
            'early_check_in_price' => str_replace(",", "", $request->earlyCheckInPrice),
            'late_check_out_price' => str_replace(",", "", $request->lateCheckOutPrice),
            'free_breakfast_price' => str_replace(",", "", $request->breakfastPrice),
            'free_lunch_price' => str_replace(",", "", $request->lunchPrice),
            'free_dinner_price' => str_replace(",", "", $request->dinnerPrice),
            'rate' => $request->rate,

        ]);

        if ($request->hotelServices) {
            foreach ($request->hotelServices as $service) {
                $service = HotelHotelService::Create([
                    'hotel_service_id' => $service,
                    'hotel_id' => $hotel->id
                ]);
            }
        }
        if ($request->roomServices) {
            foreach ($request->roomServices as $service) {
                $service = HotelRoomService::Create([
                    'room_service_id' => $service,
                    'hotel_id' => $hotel->id
                ]);
            }
        }

        return Redirect::route('hotels.index')->with('success', 'هتل با موفقیت ایجاد شد');


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
        $provinces = ProvinceCity::where('parent', 0)->orderBy('title','ASC')->get();
        $hotel = Hotel::findOrFail($id);
        $hotel->cityPlace;
        $hotel->image;
        $hotel->roomServices;
        $hotel->hotelServices;

        $hotelServices = HotelService::all();
        $roomServices = RoomService::all();
        return Inertia::render('hotels/Create', [
            "provinces" => $provinces,
            "data" => $hotel,
            "hotelServices" => $hotelServices,
            "roomServices" => $roomServices
        ]);
    }


    public function update(Request $request, $id)
    {
        //

        $request->validate([
            'name' => 'required',
            'stars' => 'required',
            'type' => 'required',
            'address' => 'required',
            'city_id' => 'required',
            'province_id' => 'required',
            'latitude' => 'required',
            // 'longitude' => 'required',
            'description' => 'required',
            'check_in' => 'required',
            'check_out' => 'required',
            // 'image' => 'required',
            'notes' => 'required',
            'capacity' => 'required',
            'available_time_from' => 'required',
            'available_time_to' => 'required',
            'adultPrice' => 'required',
            'teenPrice' => 'required',
            'kidPrice' => 'required',
            'infantPrice' => 'required',
            'fullboardPrice' => 'required',
            'earlyCheckInPrice' => 'required',
            'lateCheckOutPrice' => 'required',
            'breakfastPrice' => 'required',
            'lunchPrice' => 'required',
            'dinnerPrice' => 'required',
            'rate' => 'required',
        ]);
        // dd($request->all());  
        $hotel = Hotel::findOrFail($id);
        $image = null;

        if ($request->image) {
            if ($hotel->image_id != null) {
                @unlink(public_path() . $hotel->image->relative_url);
                $file_id = $hotel->image->id;
                // File::findOrFail($file_id)->delete();
            }
            $md5Name = md5_file($request->file('image')->getRealPath());
            $guessExtension = $request->file('image')->guessExtension();
            $logoFile = $request->file('image')->storeAs('/hotel', $md5Name . '.' . $guessExtension, 'public');

            $image = File::create([
                'name' => $md5Name,
                'path' => '/uploads/hotel',
                'mimeType' => $guessExtension,
            ]);
        } else {
            $image = $hotel->image;
        }
        // return array_values($request->latitude)[0];
        // dd($request->all());
        $lat = array_values($request->latitude)[1];
        $long = array_values($request->latitude)[0];
        $hotel = Hotel::updateOrCreate(["id" => $id], [
            'name' => $request->name,
            'stars' => $request->stars,
            'type' => $request->type,
            'address' => $request->address,
            'city_id' => $request->city_id,
            'province_id' => $request->province_id,
            'latitude' => $lat,
            'longitude' => $long,
            'description' => $request->description,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'image_id' => $image ? $image->id : $image,
            'notes' => $request->notes,
            'capacity' => $request->capacity,
            'available_time_from' => $request->available_time_from,
            'available_time_to' => $request->available_time_to,
            'adult_price' => str_replace(",", "", $request->adultPrice),
            'teen_price' => str_replace(",", "", $request->teenPrice),
            'kid_price' => str_replace(",", "", $request->kidPrice),
            'infant_price' => str_replace(",", "", $request->infantPrice),
            'fullboard_price' => str_replace(",", "", $request->fullboardPrice),
            'early_check_in_price' => str_replace(",", "", $request->earlyCheckInPrice),
            'late_check_out_price' => str_replace(",", "", $request->lateCheckOutPrice),
            'free_breakfast_price' => str_replace(",", "", $request->breakfastPrice),
            'free_lunch_price' => str_replace(",", "", $request->lunchPrice),
            'free_dinner_price' => str_replace(",", "", $request->dinnerPrice),
            'rate' => $request->rate,

        ]);
        HotelHotelService::where('hotel_id', $hotel->id)->delete();
        HotelRoomService::where('hotel_id', $hotel->id)->delete();

        if ($request->hotelServices) {
            foreach ($request->hotelServices as $service) {
                $service = HotelHotelService::Create([
                    'hotel_service_id' => $service,
                    'hotel_id' => $hotel->id
                ]);
            }
        }
        if ($request->roomServices) {
            foreach ($request->roomServices as $service) {
                $service = HotelRoomService::Create([
                    'room_service_id' => $service,
                    'hotel_id' => $hotel->id
                ]);
            }
        }

        return Redirect::route('hotels.index')->with('success', 'هتل با موفقیت ایجاد شد');

    }

    public function destroy($id)
    {
        //

        $hotel = Hotel::findOrFail($id);
        $fileId = $hotel->image_id;
        $hotel->delete();

        //delete hotel
        return Redirect::route('hotels.index')->with('success', 'هتل با موفقیت حذف شد');
    }

    public function active($id)
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->update([
            'active' => $hotel->active == 1 ? 0 : 1
        ]);
        return Redirect::route('hotels.index')->with('success', 'هتل با موفقیت تغییر وضعیت کرد');
    }

    //gallery functions
    public function gallery($id)
    {
        $hotel = Hotel::findOrFail($id);
        return Inertia::render('hotels/gallery', [
            'hotel' => $hotel,
            'images' => $hotel->hotelImages,
        ]);
    }

    //save file
    public function saveFile(Request $request, $hotel_id)
    {
        $hotel = Hotel::findOrFail($hotel_id);
        $request->validate([
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:5048',
        ]);
        if ($request->image) {
            //accept only images

            $md5Name = md5_file($request->file('image')->getRealPath());
            $guessExtension = $request->file('image')->guessExtension();
            $logoFile = $request->file('image')->storeAs('/hotel/' . $hotel_id, $md5Name . '.' . $guessExtension, 'public');

            $image = File::create([
                'name' => $md5Name,
                'path' => '/uploads/hotel/' . $hotel_id,
                'mimeType' => $guessExtension,
            ]);

            //save file id in hotel images table
            $hotelImages = HotelGallery::create([
                'hotel_id' => $hotel_id,
                'file_id' => $image->id,
            ]);
        }
        return Redirect::route('hotels.gallery', $hotel->id)->with('success', 'فایل با موفقیت ذخیره شد');
    }

    //delete file
    public function deleteFile($id)
    {
        $hotelImage = HotelGallery::findOrFail($id);
        $file = File::findOrFail($hotelImage->file_id);
        @unlink(public_path() . $file->relative_url);
        $hotel_id = $hotelImage->hotel_id;
        $hotelImage->delete();
        $file->delete();
        return Redirect::route('hotels.gallery', $hotel_id)->with('success', 'فایل با موفقیت حذف شد');
    }

}