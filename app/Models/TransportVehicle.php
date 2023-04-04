<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportVehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'from_city',
        'to_city',
        'transport_company_id',
        'departure_date_time',
        'arrival_date_time',
        'transport_type',
        'transport_number',
        'transport_class',
        'capacity',
        'terminal',
        'adult_price',
        'teen_price',
        'kid_price',
        'infant_price',
        'meta',
        'active',
        'used_count'
    ];

    //append attributes
    // protected $appends = [
    //     'departure_date_time',
    //     'arrival_date_time'
    // ];

    //hasone city relation function
    public function fromCity()
    {
        return $this->hasOne(ProvinceCity::class, 'id', 'from_city');
    }

    //hasone city relation function
    public function toCity()
    {
        return $this->hasOne(ProvinceCity::class, 'id', 'to_city');
    }

    //hasone transport company relation function
    public function transportCompany()
    {
        return $this->hasOne(TransportCompany::class, 'id', 'transport_company_id');
    }

// //convert unix date time to persian date time
// public function getDepartureDateTimeAttribute($value)
// {
//     return jdate($value)->format('%A, %d %B %y');
// }

// //convert unix date time to persian date time   
// public function getArrivalDateTimeAttribute($value)
// {
//     return jdate($value)->format('%A, %d %B %y');
// }


}