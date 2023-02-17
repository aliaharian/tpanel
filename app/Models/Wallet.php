<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    use HasFactory;

    //fillable
    protected $fillable = [
        'user_id',
        'amount',
    ];

    //user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    //transactions
    public function transactions()
    {
        //order by id desc
        return $this->hasMany(WalletTransactions::class)->orderBy('id', 'desc');
    }
}