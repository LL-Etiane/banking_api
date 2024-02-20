<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_number',
        'balance',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function debit_transactions()
    {
        return $this->hasMany(Transactions::class, 'debit');
    }

    public function credit_transactions()
    {
        return $this->hasMany(Transactions::class, 'credit');
    }
}
