<?php

namespace App\Http\Controllers;

use App\Models\TourService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TourServiceController extends Controller
{
    public function index()
    {
        //
        $tourServices = TourService::orderBy('id', 'desc')->get();
        return Inertia::render('services/ServicesList', [
            'services' => $tourServices
        ]);
    }


    public function create()
    {
        return Inertia::render('services/CreateService');
    }


    public function store(Request $request)
    {
        //

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required'
        ]);


        $service = TourService::create([
            'name' => $request->name,
            'price' => str_replace(',', '', $request->price)
        ]);
        return Redirect::route('tourServices.create')->with('success', 'خدمت با موفقیت ایجاد شد');

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

        $service = TourService::findOrFail($id);
        return Inertia::render('services/CreateService', [
            'service' => $service
        ]);

    }


    public function update(Request $request, $id)
    {
        //

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required'

        ]);


        $service = TourService::updateOrCreate(['id' => $id], [
            'name' => $request->name,
            'price' => str_replace(',', '', $request->price)
        ]);
        return Redirect::route('tourServices.index')->with('success', 'خدمت با موفقیت ویرایش شد');


    }

    public function destroy($id)
    {
        $service = TourService::destroy($id);
        return Redirect::route('tourServices.index')->with('success', 'خدمت با موفقیت حذف شد');

    }
}