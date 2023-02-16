<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintFactor extends Model
{
    use HasFactory;
    protected $fillable = [
        'url_hash',
        'watcher_id'
    ];

}