<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BankAccount;

class CustomerManagementController extends Controller
{
    public function view_customer(Request $request, $user_id){
        $user = User::findOrfail($user_id)->load('bank_accounts');

        return response([
            'user' => $user,
        ]);
    }

    /**
     * Transfer money between accounts by an employee of the bank
     */
    public function transfer_money(Request $request){
        $validated = $request->validate([
            'from_account' => 'required|exists:bank_accounts,account_number',
            'to_account' => 'required|exists:bank_accounts,account_number',
            'amount' => 'required|numeric|min:0.01',
        ]);

        // Verify to make sure from and to are no the same account
        if($validated['from_account'] == $validated['to_account']){
            return response([
                'message' => 'You cannot transfer money to the same account',
            ], 400);
        }

        $from_account = BankAccount::where('account_number', $validated['from_account'])->first();
        $to_account = BankAccount::where('account_number', $validated['to_account'])->first();

        // Verify to make sure the from account has enough money
        if($from_account->balance < $validated['amount']){
            return response([
                'message' => 'The from account does not have enough money',
            ], 400);
        }

        $from_account->balance -= $validated['amount'];
        $to_account->balance += $validated['amount'];

        $from_account->save();
        $to_account->save();

        $from_account->debit_transactions()->create([
            'credit' => $to_account->id,
            'amount' => $validated['amount'],
        ]);

        $to_account->credit_transactions()->create([
            'debit' => $from_account->id,
            'amount' => $validated['amount'],
        ]);

        return response([
            'message' => 'Money transferred successfully',
        ]);
    }
}
