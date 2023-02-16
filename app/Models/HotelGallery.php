<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelGallery extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'file_id',
    ];

    //append
    protected $appends = ['file'];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotel_id');
    }

    public function file()
    {
        return $this->belongsTo(File::class, 'file_id');
    }

    //file attribute
    public function getFileAttribute()
    {
        return $this->file()->first();
    }
}
