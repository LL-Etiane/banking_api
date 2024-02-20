<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

    protected $fillable = [
        'debit',
        'credit',
        'amount',
    ];

    public function from_account(){
        return $this->belongsTo(BankAccount::class, 'from_account');
    }

}
