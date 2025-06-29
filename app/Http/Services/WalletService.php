<?php
    namespace App\Http\Services;

    use App\Repositories\WalletTransactionRepository;

    class WalletService
    {
        public function __construct(WalletTransactionRepository $WalletTransactionRepository)
        {
            $this->WalletTransactionRepository = $WalletTransactionRepository;
        }

        public function save($data)
        {
           /* $user=User::where('id',$data['user_id'])->first();
            $user->wallet_balance +=  $data['amount'];
            $user->save();*/
            //save wallet transaction
            $this->WalletTransactionRepository->save($data);
        }
    }
