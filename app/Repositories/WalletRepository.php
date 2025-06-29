<?php


namespace App\Repositories;

use App\Http\Resources\Api\VehicleResource;
use App\Http\Resources\Api\WalletResource;
use App\Models\Vehicle;
use App\Traits\HandleResponse;
use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\Operator;
use Validator;

class WalletRepository
{
    use HandleResponse;


    public function add_wallet_balance($request)
    {

        $user = Operator::findOrFail(auth()->user()->id);
        if ($user && $user->operator) {
            $validator = Validator::make($request->all(), [
                'balance' => ['required', 'numeric'],
            ]);
            if ($validator->fails()) {
                return $this->send_response(false, 400, $validator->errors()->first(), null);
            }

            $wallet = Wallet::whereOperatorId(auth()->user()->id)->first();
            if ($wallet) {
                $wallet->balance += $request->balance;
                $wallet->save();
            } else {
                $wallet = new Wallet();
                $wallet->operator_id = auth()->user()->id;
                $wallet->balance = $request->balance;
                $wallet->save();
            }
            return $this->send_response(TRUE, 200, 'success', WalletResource::make($wallet));
        }
        return $this->send_response(FALSE, 400, 'NO OPERATORE REGISTRED WITH YOUR CREDENTIALS', NULL);
    }

    public function wallet_details()
    {

        $wallet = Wallet::where('operator_id', auth()->user()->id)->first();
        if ($wallet) {
            return $this->send_response(TRUE, 200, 'success', new WalletResource($wallet));
        }
        return $this->send_response(FALSE, 400, 'NO wallet found', NULL);
    }
}
