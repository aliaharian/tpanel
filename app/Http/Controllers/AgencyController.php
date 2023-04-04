<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\File;
use App\Models\ProvinceCity;
use App\Models\User;
use App\Models\Watcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AgencyController extends Controller
{
    public function index()
    {
        $agencies = Agency::all();

        return Inertia::render('agencies/AgenciesList', [
            'agencies' => $agencies
        ]);
    }
    public function create()
    {
        return Inertia::render('agencies/CreateAgency');
    }

    public function store(Request $request)
    {

        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'agencyName' => 'required|string|max:255',
            // 'agencyLogo' => '',
            'mobile' => 'required|max:255|unique:users,mobile',
            'email' => 'required|string|max:255|unique:users,email',
            'offPercent' => 'required',
            'markupPercent' => 'required',
        ]);


        if ($request->markupPercent > 0 && !$request->agencyLogo) {
            return Redirect::back()->with('error', 'لوگوی نمایندگی الزامی است');
        }
        $agencyLogo = null;
        if ($request->agencyLogo) {
            $md5Name = md5_file($request->file('agencyLogo')->getRealPath());
            $guessExtension = $request->file('agencyLogo')->guessExtension();
            $logoFile = $request->file('agencyLogo')->storeAs('/agency_logo', $md5Name . '.' . $guessExtension, 'public');

            $agencyLogo = File::create([
                'name' => $md5Name,
                'path' => '/uploads/agency_logo',
                'mimeType' => $guessExtension,
            ]);
        }

        $user = User::create([
            'name' => $request->firstName,
            'last_name' => $request->lastName,
            'email' => $request->email,
            'mobile' => $request->mobile,
            // 'password',

        ]);
        $watcher = Agency::create([
            'user_id' => $user->id,
            'agency_name' => $request->agencyName,
            'agency_logo' => $agencyLogo ? $agencyLogo->id : $agencyLogo,
            'agency_off_percent' => $request->offPercent,
            'agency_markup_percent' => $request->markupPercent,
            'status' => 1,
            'showLogo' => ($request->markupPercent > 0 || $request->showLogo === 'true') ? 1 : 0

        ]);
        $agencies = Agency::all();

        return Inertia::render('agencies/AgenciesList', [
            'agencies' => $agencies
        ]);
        // return Inertia::render('agencies/CreateAgency');
    }
    public function edit($id)
    {
        $agency = Agency::findOrFail($id);
        $agency->user;
        $agency->logo;
        return Inertia::render('agencies/CreateAgency', [
            'agency' => $agency
        ]);
    }

    public function update($id, Request $request)
    {

        $agency = Agency::findOrFail($id);
        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'agencyName' => 'required|string|max:255',
            // 'agencyLogo' => '',
            'mobile' => 'required|max:255|unique:users,mobile,' . $agency->user_id,
            'email' => 'required|string|max:255|unique:users,email,' . $agency->user_id,
            'offPercent' => 'required',
            'markupPercent' => 'required',

        ]);

        if ($request->markupPercent > 0 && !$request->agencyLogo && !$agency->agency_logo) {
            return Redirect::back()->with('error', 'لوگوی نمایندگی الزامی است');
        }

        $agencyLogo = null;

        // return ($agency->logo);
        if ($request->agencyLogo) {
            if ($agency->agency_logo != null) {
                @unlink(public_path() . $agency->logo->relative_url);
                $file_id = $agency->logo->id;
                File::findOrFail($file_id)->delete();
            }
            $md5Name = md5_file($request->file('agencyLogo')->getRealPath());
            $guessExtension = $request->file('agencyLogo')->guessExtension();
            $logoFile = $request->file('agencyLogo')->storeAs('/agency_logo', $md5Name . '.' . $guessExtension, 'public');

            $agencyLogo = File::create([
                'name' => $md5Name,
                'path' => '/uploads/agency_logo',
                'mimeType' => $guessExtension,
            ]);
        } else {
            $agencyLogo = $agency->logo;
        }

        $user = User::updateOrCreate(['id' => $agency->user_id], [
            'name' => $request->firstName,
            'last_name' => $request->lastName,
            'email' => $request->email,
            'mobile' => $request->mobile,
            // 'password',
        ]);
        $watcher = Agency::updateOrCreate(['id' => $agency->id], [
            'user_id' => $user->id,
            'agency_name' => $request->agencyName,
            'agency_logo' => $agencyLogo ? $agencyLogo->id : $agencyLogo,
            'showLogo' => ($request->markupPercent > 0 || $request->showLogo === 'true') ? 1 : 0,
            'agency_off_percent' => $request->offPercent,
            'agency_markup_percent' => $request->markupPercent,
        ]);
        $agencies = Agency::all();

        return Inertia::render('agencies/AgenciesList', [
            'agencies' => $agencies
        ]);
        // return Inertia::render('agencies/CreateAgency');
    }

    public function loadCity(Request $request)
    {
        $cities = ProvinceCity::where('parent', $request->province)->orderBy('title','ASC')->get();
        return response()->json($cities);
    }
    public function destroy($id)
    {
        $watcher = Watcher::where("agency_id", $id)->get();
        if($watcher->count() > 0){
            return Redirect::back()->with('error', 'آژانس دارای واچر می باشد');
        }
        $agency = Agency::findOrFail($id);
        $user = User::destroy($agency->user_id);
        Agency::destroy($id);
        return Redirect::route('agencies')->with('success', 'آژانس با موفقیت حذف شد');

    }

    public function loadWatchers()
    {
        $agency = Agency::where('user_id', Auth::user()->id)->first();
        if (!$agency) {
            return abort('404');
        }
        $watchers = Watcher::where('agency_id', $agency->id)->get();
        foreach ($watchers as $watcher) {
            $watcher->from_city;
            $watcher->to_city;
            $watcher->departure_vehicle;
            $watcher->arrival_vehicle;
        }
        return Inertia::render('agenciesDashboard/WatchersList', [
            'watchers' => $watchers,
            'agency' => $agency
        ]);


    }
    public function agencyDashboard()
    {
        $agency = Agency::where('user_id', Auth::user()->id)->first();
        if (!$agency) {
            return abort('404');
        }
        $agency->user;

        return Inertia::render('agenciesDashboard/Dashboard', ['info' => $agency]);
    }


    public function agencySetting()
    {
        $user = Auth::user();
        $agency = Agency::where('user_id', $user->id)->first();
        $agency->logo;
        return Inertia::render('agenciesDashboard/AgencySetting', [
            'user' => $user,
            'agency' => $agency
        ]);


    }

    public function agencySettingUpdate(Request $request)
    {
        $user = Auth::user();
        $agency = Agency::where('user_id', $user->id)->first();
        $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'agencyName' => 'required|string|max:255',
            // 'agencyLogo' => '',
            'mobile' => 'required|max:255|unique:users,mobile,' . $agency->user_id,
            'email' => 'required|string|max:255|unique:users,email,' . $agency->user_id,
            // 'offPercent' => 'required',
            'markupPercent' => 'required',
        ]);




        if ($request->markupPercent > 0 && !$request->agencyLogo && !$agency->agency_logo) {
            return Redirect::back()->with('error', 'لوگوی نمایندگی الزامی است');
        }

        $agencyLogo = null;

        // return ($agency->logo);
        if ($request->agencyLogo) {
            if ($agency->agency_logo != null) {
                @unlink(public_path() . $agency->logo->relative_url);
                $file_id = $agency->logo->id;
                File::findOrFail($file_id)->delete();
            }
            $md5Name = md5_file($request->file('agencyLogo')->getRealPath());
            $guessExtension = $request->file('agencyLogo')->guessExtension();
            $logoFile = $request->file('agencyLogo')->storeAs('/agency_logo', $md5Name . '.' . $guessExtension, 'public');

            $agencyLogo = File::create([
                'name' => $md5Name,
                'path' => '/uploads/agency_logo',
                'mimeType' => $guessExtension,
            ]);
        } else {
            $agencyLogo = $agency->logo;
        }



        $user = User::updateOrCreate(['id' => $user->id], [
            'name' => $request->firstName,
            'last_name' => $request->lastName,
            'email' => $request->email,
            'mobile' => $request->mobile,
            // 'password',

        ]);
        $agency = Agency::updateOrCreate(['id' => $agency->id], [
            'user_id' => $user->id,
            'agency_name' => $request->agencyName,
            'agency_logo' => $agencyLogo ? $agencyLogo->id : $agencyLogo,
            'showLogo' => ($request->markupPercent > 0 || $request->showLogo === 'true') ? 1 : 0,
            'agency_off_percent' => $request->offPercent,
            'agency_markup_percent' => $request->markupPercent > 0 ? $request->markupPercent : 0,
            'status' => 1,
        ]);
        $agencies = Agency::all();

        $agency->logo;

        return Redirect::back()->with('success', 'اطلاعات با موفقیت ثبت شد');

        // return Inertia::render('agenciesDashboard/AgencySetting', [
        //     'user' => $user,
        //     'agency' => $agency
        // ]);
    }

    public function loadSingleWatcher($id)
    {
        $agency = Agency::where('user_id', Auth::user()->id)->first();
        $watcher = Watcher::findOrFail($id);

        if ($agency->id == $watcher->agency_id) {
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
                'totalPrice' => number_format($watcher->total_price),
                'agencyPrice' => number_format($watcher->agency_price),
                'userPrice' => number_format($watcher->user_price),
                'admin' => false,
                'agency' => true,
                'baseUrl' => env('APP_URL'),
                'showLogo'=>$watcher->show_logo

            ]);
        } else {
            abort(401);
        }

    }
}