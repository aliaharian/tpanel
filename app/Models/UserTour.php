<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTour extends Model
{
    use HasFactory;
    //fillable
    protected $fillable = [
        'from_city_id',
        'to_city_id',
        'departure_date_time',
        'arrival_date_time',
        'adult_count',
        'teen_count',
        'kid_count',
        'infant_count',
        'hotel_id',
        'departure_vehicle_id',
        'arrival_vehicle_id',
        'agency_id',
        'user_id',
        'rooms_count',
        'rooms_name',
        'fullboard',
        'status_id',
        'transaction_id',
        'payed',
        'payablePrice'
    ];

    //from city
    public function fromCity()
    {
        return $this->belongsTo(ProvinceCity::class, 'from_city_id');
    }

    //to city
    public function toCity()
    {
        return $this->belongsTo(ProvinceCity::class, 'to_city_id');
    }

    //hotel
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    //departure vehicle
    public function departureVehicle()
    {
        return $this->belongsTo(TransportVehicle::class, 'departure_vehicle_id');
    }

    //arrival vehicle
    public function arrivalVehicle()
    {
        return $this->belongsTo(TransportVehicle::class, 'arrival_vehicle_id');
    }

    //agency
    public function agency()
    {
        return $this->belongsTo(Agency::class, 'agency_id');
    }

    //user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    //status
    public function status()
    {
        return $this->belongsTo(TourStatus::class, 'status_id');
    }

    //services

    public function services()
    {
        return $this->belongsToMany(TourService::class , "user_tour_services");
    }

    //rooms
    public function rooms()
    {
        return $this->hasMany(TourRoom::class , "tour_rooms" , "tour_id" );
    }


}