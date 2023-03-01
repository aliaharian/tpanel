<?php

namespace App\Http\Controllers;

use App\Models\File;
use App\Models\TransportCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TransportAgenciesController extends Controller
{

    public function index()
    {
        //
        $companies = TransportCompany::all();
        foreach ($companies as $co) {
            $co->logo;
        }
        return Inertia::render('transportCompanies/Index', [
            'companies' => $companies
        ]);
    }


    public function create()
    {
        return Inertia::render('transportCompanies/Create');
    }

    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
        ]);

        $logo = null;

        if ($request->logo) {

            //get file size
            $fileSize = $request->file('logo')->getSize();
            //file not exeed 800 kb
            if ($fileSize > 800000) {
                return Redirect::route('transportCompanies.index')->with('error', 'حجم فایل باید کمتر از 800 کیلوبایت باشد');
            }
          
            //get file extension
            $fileExtension = $request->file('logo')->getClientOriginalExtension();
            //file extension must be jpg or png
            if ($fileExtension != 'jpg' && $fileExtension != 'png') {
                return Redirect::route('transportCompanies.index')->with('error', 'فرمت فایل باید jpg یا png باشد');
            }
            
            $md5Name = md5_file($request->file('logo')->getRealPath()).max(1,rand(1,100));
            $guessExtension = $request->file('logo')->guessExtension();
            $logoFile = $request->file('logo')->storeAs('/transport_company', $md5Name . '.' . $guessExtension, 'public');

            $logo = File::create([
                'name' => $md5Name,
                'path' => '/uploads/transport_company',
                'mimeType' => $guessExtension,
            ]);
        }

        $co = TransportCompany::create([
            'name' => $request->name,
            'transport_type' => $request->type,
            'logo_id' => $logo ? $logo->id : $logo,
            'active' => 1
        ]);
        return Redirect::route('transportCompanies.index')->with('success', 'شرکت حمل و نقل با موفقیت ایجاد شد');
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

        $co = TransportCompany::findOrFail($id);
        $co->logo;
        //
        return Inertia::render('transportCompanies/Create', ['co' => $co]);
    }

    public function update(Request $request, $id)
    {
        //
        //
        $company = TransportCompany::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
        ]);

        $logo = null;

        if ($request->logo) {
            //get file size
            $fileSize = $request->file('logo')->getSize();
            //file not exeed 800 kb
            if ($fileSize > 800000) {
                return Redirect::route('transportCompanies.index')->with('error', 'حجم فایل باید کمتر از 800 کیلوبایت باشد');
            }
            //get file extension
            $fileExtension = $request->file('logo')->getClientOriginalExtension();
            //file extension must be jpg or png
            if ($fileExtension != 'jpg' && $fileExtension != 'png') {
                return Redirect::route('transportCompanies.index')->with('error', 'فرمت فایل باید jpg یا png باشد');
            }

            if ($company->logo_id != null) {
                @unlink(public_path() . $company->logo->relative_url);
                $file_id = $company->logo->id;
                // File::findOrFail($file_id)->delete();
            }
            $md5Name = md5_file($request->file('logo')->getRealPath()).max(1,rand(1,100));
            $guessExtension = $request->file('logo')->guessExtension();
            $logoFile = $request->file('logo')->storeAs('/transport_company', $md5Name . '.' . $guessExtension, 'public');

            $logo = File::create([
                'name' => $md5Name,
                'path' => '/uploads/transport_company',
                'mimeType' => $guessExtension,
            ]);
        } else {
            $logo = $company->logo;
        }
        $co = TransportCompany::updateOrCreate(['id' => $id], [
            'name' => $request->name,
            'transport_type' => $request->type,
            'logo_id' => $logo->id,
            'active' => $request->active
        ]);
        return Redirect::route('transportCompanies.index')->with('success', 'شرکت حمل و نقل با موفقیت ویرایش شد');
    }

    public function destroy($id)
    {
        $company = TransportCompany::findOrFail($id);
        $company->logo;
        if ($company->logo_id != null) {
            //unlink public_path() . $company->logo->relative_url if exist
            @unlink(public_path() . $company->logo->relative_url);
        }
        $company->delete();
        if ($company->logo_id != null) {
            $file_id = $company->logo->id;
            File::findOrFail($file_id)->delete();
        }
        $companies = TransportCompany::all();
        foreach ($companies as $co) {
            $co->logo;
        }
        return Inertia::render('transportCompanies/Index', [
            'companies' => $companies
        ]);
    }

    public function active($id)
    {
        $company = TransportCompany::findOrFail($id);
        $companyEdit = TransportCompany::updateOrCreate(['id' => $id], ['active' => $company->active == 1 ? 0 : 1]);
        return Redirect::route('transportCompanies.index')->with('success', 'شرکت حمل و نقل با موفقیت ویرایش شد'); 
    }
}
