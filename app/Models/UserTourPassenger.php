<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTourPassenger extends Model
{

    use HasFactory;

    //fillable
    protected $fillable = [
        'user_tour_id',
        'passenger_id',
    ];
}

