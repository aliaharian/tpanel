<?php

namespace App\Http\Controllers;

use App\Models\ProvinceCity;
use App\Models\TransportCompany;
use App\Models\TransportVehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class TransportVehicleController extends Controller
{

    public function index()
    {
        //
        $vehicles = TransportVehicle::all();

        foreach ($vehicles as $vehicle) {
            $vehicle->fromCity;
            $vehicle->toCity;
            $vehicle->transportCompany;
        }

        return Inertia::render('transportVehicles/Index', [
            'vehicles' => $vehicles
        ]);
    }


    public function create()
    {
        //
        $provinces = ProvinceCity::where('parent', 0)->get();
        $transportCompanies = TransportCompany::all();

        return Inertia::render('transportVehicles/Create', [
            'provinces' => $provinces,
            'transportCompanies' => $transportCompanies
        ]);

    }


    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'fromProvince' => 'required',
            'fromCity' => 'required',
            'toProvince' => 'required',
            'toCity' => 'required',
            'type' => 'required',
            'transportCompany' => 'required',
            'departure_date' => 'required',
            'arrival_date' => 'required',
            'transportNumber' => 'required',
            'transportClass' => 'required',
            'capacity' => 'required',
            'adultPrice' => 'required',
            'teenPrice' => 'required',
            'kidPrice' => 'required',
            'infantPrice' => 'required',
        ]);

        //save to TransportVehicles with Create method get items from TransportCompany model
        TransportVehicle::create([
            'name' => $request->name,
            'from_city' => $request->fromCity,
            'to_city' => $request->toCity,
            'transport_company_id' => $request->transportCompany,
            'departure_date_time' => $request->departure_date,
            'arrival_date_time' => $request->arrival_date,
            'transport_type' => $request->type,
            'transport_number' => $request->transportNumber,
            'transport_class' => $request->transportClass,
            'capacity' => $request->capacity,
            'adult_price' => str_replace(',', '', $request->adultPrice),
            'teen_price' => str_replace(',', '', $request->teenPrice),
            'kid_price' => str_replace(',', '', $request->kidPrice),
            'infant_price' => str_replace(',', '', $request->infantPrice),
            // 'meta' => $request->meta,
            // 'active' => $request->active,
        ]);

        return Redirect::route('transportVehicles.index')->with('success', 'وسیله حمل و نقل با موفقیت ایجاد شد');
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
        $co = TransportVehicle::findOrFail($id);
        $co->fromCity;
        $co->toCity;
        $co->transportCompany;
        $provinces = ProvinceCity::where('parent', 0)->get();
        $transportCompanies = TransportCompany::all();

        //
        return Inertia::render('transportVehicles/Create', [
            'data' => $co,
            'provinces' => $provinces,
            'transportCompanies' => $transportCompanies
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'fromProvince' => 'required',
            'fromCity' => 'required',
            'toProvince' => 'required',
            'toCity' => 'required',
            'type' => 'required',
            'transportCompany' => 'required',
            'departure_date' => 'required',
            'arrival_date' => 'required',
            'transportNumber' => 'required',
            'transportClass' => 'required',
            'capacity' => 'required',
            'adultPrice' => 'required',
            'teenPrice' => 'required',
            'kidPrice' => 'required',
            'infantPrice' => 'required',
        ]);

        //save to TransportVehicles with Create method get items from TransportCompany model
        TransportVehicle::updateOrCreate(['id' => $id], [
            'name' => $request->name,
            'from_city' => $request->fromCity,
            'to_city' => $request->toCity,
            'transport_company_id' => $request->transportCompany,
            'departure_date_time' => $request->departure_date,
            'arrival_date_time' => $request->arrival_date,
            'transport_type' => $request->type,
            'transport_number' => $request->transportNumber,
            'transport_class' => $request->transportClass,
            'capacity' => $request->capacity,
            'adult_price' => str_replace(',', '', $request->adultPrice),
            'teen_price' => str_replace(',', '', $request->teenPrice),
            'kid_price' => str_replace(',', '', $request->kidPrice),
            'infant_price' => str_replace(',', '', $request->infantPrice),
        
            // 'meta' => $request->meta,
            // 'active' => $request->active,
        ]);

        return Redirect::route('transportVehicles.index')->with('success', 'وسیله حمل و نقل با موفقیت به روز شد');
    }


    public function destroy($id)
    {
        //

        TransportVehicle::destroy($id);
        return Redirect::route('transportVehicles.index')->with('success', 'وسیله حمل و نقل با موفقیت حذف شد');
    }

    //loadTransportCompanies 
    public function loadTransportCompanies(Request $request)
    {
        $transportCompanies = TransportCompany::where('transport_type', $request->type)->get();
        return response()->json($transportCompanies);
    }
    public function loadTransportVehicles(Request $request)
    {
        $transportVehicles = TransportVehicle::where('transport_type', $request->type)
            ->where('from_city', $request->from)
            ->where('to_city', $request->to)
            ->get();
        foreach ($transportVehicles as $key => $value) {
            $value->transportCompany;
        }


        return response()->json($transportVehicles);
    }


    public function active($id)
    {
        $company = TransportVehicle::findOrFail($id);
        $companyEdit = TransportVehicle::updateOrCreate(['id' => $id], ['active' => $company->active == 1 ? 0 : 1]);
        return Redirect::route('transportVehicles.index')->with('success', 'وسیله حمل و نقل با موفقیت ویرایش شد');
    }
}