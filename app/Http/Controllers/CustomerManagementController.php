<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\BankAccount;

class CustomerManagementController extends Controller
{
    public function view_customer(Request $request, $user_id)
    {
        $user = User::findOrfail($user_id)->load('bank_accounts');

        return response([
            'user' => $user,
        ]);
    }

    public function add_account(Request $request, $user_id)
    {
        $user = User::findOrfail($user_id);
        $request->validate([
            'initial_balance' => 'required|numeric|min:0',
        ]);

        $account = $user->bank_accounts()->create([
            'account_number' => rand(1000000000, 9999999999),
            'balance' => $request->initial_balance,
        ]);

        return response([
            'account' => $account,
        ]);
    }

    /**
     * Transfer money between accounts by an employee of the bank
     */
    public function transfer_money(Request $request)
    {
        $validated = $request->validate([
            'from_account' => 'required|exists:bank_accounts,account_number',
            'to_account' => 'required|exists:bank_accounts,account_number',
            'amount' => 'required|numeric|min:0.01',
        ]);

        // Verify to make sure from and to are no the same account
        if ($validated['from_account'] == $validated['to_account']) {
            return response(
                [
                    'message' => 'You cannot transfer money to the same account',
                ],
                400,
            );
        }

        $from_account = BankAccount::where('account_number', $validated['from_account'])->first();
        $to_account = BankAccount::where('account_number', $validated['to_account'])->first();

        // Verify to make sure the from account has enough money
        if ($from_account->balance < $validated['amount']) {
            return response(
                [
                    'message' => 'The from account does not have enough money',
                ],
                400,
            );
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

    public function view_account(Request $request, $account_number)
    {
        $account = BankAccount::where('account_number', $account_number)->first();

        return response([
            'account' => $account,
        ]);
    }

    public function view_account_transactions(Request $request, $account_number)
    {
        $account = BankAccount::where('account_number', $account_number)->first();
        $transactions = $account->debit_transactions
            ->concat($account->credit_transactions)
            ->unique('transaction_id')
            ->sortByDesc('created_at')
            ->load(['debit_account', 'credit_account']);

        $results = [];

        foreach ($transactions as $transaction) {
            $results[] = [
                'transaction_id' => $transaction->id,
                'amount' => $transaction->amount,
                'debit' => $transaction->debit_account->account_number,
                'credit' => $transaction->credit_account->account_number,
                'created_at' => $transaction->created_at,
                'type' => $transaction->debit_account->account_number == $account_number ? 'debit' : 'credit',
            ];
        }

        return response($results);
    }
}
