<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agency extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'agency_name',
        'agency_logo',
        'agency_off_percent',
        'agency_markup_percent',
        'showLogo',
        'status',
    ];
    public function user(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function logo()
    {
        return $this->hasOne(File::class, 'id', 'agency_logo');
    }

}
