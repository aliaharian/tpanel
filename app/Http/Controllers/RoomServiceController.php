<?php

namespace App\Http\Controllers;

use App\Models\RoomService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class RoomServiceController extends Controller
{
    public function index()
    {
        //
        $tourServices = RoomService::orderBy('id', 'desc')->get();
        return Inertia::render('roomServices/ServicesList', [
            'services' => $tourServices
        ]);
    }


    public function create()
    {
        return Inertia::render('roomServices/CreateService');
    }


    public function store(Request $request)
    {
        //

        $request->validate([
            'name' => 'required|string|max:255',
        ]);


        $service = RoomService::create([
            'name' => $request->name,
        ]);
        return Redirect::route('roomServices.create')->with('success', 'خدمت با موفقیت ایجاد شد');

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

        $service = RoomService::findOrFail($id);
        return Inertia::render('roomServices/CreateService', [
            'service' => $service
        ]);

    }

   
    public function update(Request $request, $id)
    {
        //

        $request->validate([
            'name' => 'required|string|max:255',
        ]);


        $service = RoomService::updateOrCreate(['id' => $id], [
            'name' => $request->name,
        ]);
        return Redirect::route('roomServices.index')->with('success', 'خدمت با موفقیت ویرایش شد');


    }

    public function destroy($id)
    {
        $service = RoomService::destroy($id);
        return Redirect::route('roomServices.index')->with('success', 'خدمت با موفقیت حذف شد');

    }
}
