<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelRoomService extends Model
{
    use HasFactory;

    //fillable
    protected $fillable = [
        'hotel_id',
        'room_service_id',
    ];

    //hotel
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    //room service
    public function roomService()
    {
        return $this->belongsTo(RoomService::class, 'room_service_id');
    }
}
