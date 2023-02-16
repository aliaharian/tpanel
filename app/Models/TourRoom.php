<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TourRoom extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'capacity',
        'tour_id'
    ];

    //tour
    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }
}