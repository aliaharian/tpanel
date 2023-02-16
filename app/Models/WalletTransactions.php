<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletTransactions extends Model
{
    use HasFactory;

    //fillable
    protected $fillable = [
        'wallet_id',
        'amount',
        'type',
        'description',
    ];

    //wallet
    public function wallet()
    {
        return $this->belongsTo(Wallet::class);
    }
    //type
    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class, 'type');
    }

}
