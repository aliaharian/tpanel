<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportCompany extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'logo_id',
        'transport_type',
        'active'
    ];
    public function logo()
    {
        return $this->hasOne(File::class, 'id', 'logo_id');
    }

}