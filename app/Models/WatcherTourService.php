<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WatcherTourService extends Model
{
    use HasFactory;
    protected $fillable = [
        'tour_service_id',
        'watcher_id'
    ];
}