<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelHotelService extends Model
{
    use HasFactory;
    //fillable

    protected $fillable = [
        'hotel_id',
        'hotel_service_id',
    ];

    //hotel
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    //hotel service
    public function hotelService()
    {
        return $this->belongsTo(HotelService::class, 'hotel_service_id');
    }
}
