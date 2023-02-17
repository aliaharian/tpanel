<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    use HasFactory;
    //fillable name
    protected $fillable = [
        'user_id',
        'name',
        'last_name',
        'male',
        'phone',
        'national_code',
        'day',
        'month',
        'year'
    ];
}