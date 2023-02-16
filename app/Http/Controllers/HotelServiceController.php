<?php

namespace App\Http\Controllers;

use App\Models\HotelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class HotelServiceController extends Controller
{
    public function index()
    {
        //
        $tourServices = HotelService::orderBy('id', 'desc')->get();
        return Inertia::render('hotelServices/ServicesList', [
            'services' => $tourServices
        ]);
    }


    public function create()
    {
        return Inertia::render('hotelServices/CreateService');
    }


    public function store(Request $request)
    {
        //

        $request->validate([
            'name' => 'required|string|max:255',
        ]);


        $service = HotelService::create([
            'name' => $request->name,
        ]);
        return Redirect::route('hotelServices.create')->with('success', 'خدمت با موفقیت ایجاد شد');

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

        $service = HotelService::findOrFail($id);
        return Inertia::render('hotelServices/CreateService', [
            'service' => $service
        ]);

    }

   
    public function update(Request $request, $id)
    {
        //

        $request->validate([
            'name' => 'required|string|max:255',
        ]);


        $service = HotelService::updateOrCreate(['id' => $id], [
            'name' => $request->name,
        ]);
        return Redirect::route('hotelServices.index')->with('success', 'خدمت با موفقیت ویرایش شد');


    }

    public function destroy($id)
    {
        $service = HotelService::destroy($id);
        return Redirect::route('hotelServices.index')->with('success', 'خدمت با موفقیت حذف شد');

    }

    
}
