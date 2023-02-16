<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailableCities extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id'
    ];

    public function city()
    {
        return $this->belongsTo(ProvinceCity::class, 'city_id', 'id');
    }
}