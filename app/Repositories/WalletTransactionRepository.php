<?php

namespace App\Repositories;

use App\Http\Resources\Api\WalletTransactionCollection;
use App\Models\Operator;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Traits\HandleResponse;
use Illuminate\Http\Request;
use Validator;
use Auth;

class WalletTransactionRepository
{
    use HandleResponse;

    public function __construct()
    {

    }

    public function get_data(Request $request)
    {
        $limit = $request->limit ? $request->limit : 10;
        $wallet = Wallet::where('operator_id', auth()->user()->id)->first();
        if ($wallet) {
            $transactions = WalletTransaction::where('wallet_id', $wallet->id)->orderByDesc('id')->paginate($limit);
            return $this->send_response(
                TRUE,
                200,
                'success',
                [
                    'wallet_balance' => round($wallet->balance, 2) . '',
                    'transactions' => new WalletTransactionCollection($transactions)
                ]
            );
        }

        return $this->send_response(
            TRUE,
            200,
            'no data found',
            [
                'wallet_balance' => '0',
                'transactions' => []
            ]
        );

    }


    public function save($data)
    {
        //find wallet
        $wallet = Wallet::where('operator_id', $data['operator_id'])->first();
        $operator = Operator::find($data['operator_id']);
        $data['amount'] = $operator->operator->order_value;
        if ($wallet) {
            if ($data['amount'] > 0) {
                $wallet->balance += $data['amount'];
                $wallet->save();
                $data['wallet_id'] = $wallet->id;
                $wallet_transaction = WalletTransaction::create($data);
                return $wallet_transaction;
            }

        }
    }

}
