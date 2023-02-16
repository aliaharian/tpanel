<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Watcher extends Model
{
    use HasFactory;

    // protected $visible = [
    //     'id',
    //     'from_city',
    //     'from_city_id'
    // ];

    protected $fillable = [
        'from_city_id',
        'to_city_id',
        'departure_transport_type',

        'arrival_transport_type',

        'hotel_name',
        'room_numbers',
        'room_type',
        'breakfast',
        'fullboard',
        'stay_length',
        'price_per_adult',
        'total_price',
        'is_haghighi',
        'buyer_name',
        'buyer_national_code',
        'agent_name',
        'mobile_phone',
        'agent_phone',
        'people_count',
        'agency_id',
        'markup',
        'departure_vehicle_id',
        'arrival_vehicle_id',
    ];

    //append
    protected $appends = [
        'user_price',
        'agency_price',
        'show_logo',

    ];

    public function from_city()
    {
        return $this->hasOne(ProvinceCity::class, 'id', 'from_city_id');
    }
    public function to_city()
    {
        return $this->hasOne(ProvinceCity::class, 'id', 'to_city_id');
    }
    public function services()
    {
        return $this->belongsToMany(TourService::class, 'watcher_tour_services');
    }
    public function agency()
    {
        return $this->hasOne(Agency::class, 'id', 'agency_id');
    }

    //departure vehicle
    public function departure_vehicle()
    {
        return $this->hasOne(TransportVehicle::class, 'id', 'departure_vehicle_id');
    }

    //arrival vehicle
    public function arrival_vehicle()
    {
        return $this->hasOne(TransportVehicle::class, 'id', 'arrival_vehicle_id');
    }
    //calculate user price attribute

    public function getUserPriceAttribute($value)
    {
        if ($this->agency_id == null) {
            return $this->total_price;
        } else {
            $agency = $this->agency;
            $markup = $this->markup ?? $agency->agency_markup_percent;
            if ($markup > 100) {
                return $this->total_price + $markup;
            } else {
                return $this->total_price + (($this->total_price * $markup) / 100);
            }
        }

    }

    //calculate ageny price attribute
    public function getAgencyPriceAttribute($value)
    {
        if ($this->agency_id == null) {
            return null;
        } else {
            $agency = $this->agency;
            if ($agency->agency_off_percent > 100) {
                return $this->total_price - $agency->agency_off_percent;
            } else {
                return ($this->total_price * (100 - $agency->agency_off_percent)) / 100;
            }
        }
    }
    public function getShowLogoAttribute($value)
    {
        if ($this->agency_id == null) {
            return 0;
        } else {
            $agency = $this->agency;
            if ($agency->showLogo == 1) {
                return 1;
            }
            if ($this->markup > 0 || $agency->agency_markup_percent > 0) {
                return 1;
            }
        }
    }
}