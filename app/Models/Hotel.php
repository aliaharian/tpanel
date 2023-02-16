<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;
    //fillable

    protected $fillable = [
        'name',
        'stars',
        'type',
        'address',
        'city_id',
        'latitude',
        'longitude',
        'description',
        'check_in',
        'check_out',
        'image_id',
        'notes',
        'capacity',
        'used_capacity',
        'available_time_from',
        'available_time_to',
        'adult_price',
        'teen_price',
        'kid_price',
        'infant_price',
        'fullboard_price',
        "active",
        "early_check_in_price",
        "late_check_out_price",
        "free_breakfast_price",
        "free_lunch_price",
        "free_dinner_price",
        "rate"
    ];

    //city
    public function cityPlace()
    {
        return $this->belongsTo(ProvinceCity::class, 'city_id');
    }
    //image
    public function image()
    {
        return $this->belongsTo(File::class, 'image_id');
    }

    //room services
    public function roomServices()
    {
        return $this->belongsToMany(RoomService::class, 'hotel_room_services', 'hotel_id', 'room_service_id');
    }

    //hotel services
    public function hotelServices()
    {
        return $this->belongsToMany(HotelService::class, 'hotel_hotel_services', 'hotel_id', 'hotel_service_id');
    }

    //hotel images
    public function hotelImages()
    {
        return $this->hasMany(HotelGallery::class, 'hotel_id');
    }

}