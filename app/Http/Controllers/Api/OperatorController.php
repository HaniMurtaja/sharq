<?php


namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\OperatorRequest;
use App\Http\Controllers\Controller;
use App\Repositories\OperatorRepository;
use App\Repositories\OrderRepository;
use App\Repositories\VehicleRepository;
use App\Repositories\WalletTransactionRepository;
use Illuminate\Http\Request;
use App\Repositories\WalletRepository;
use Validator;
use Auth;

class OperatorController extends Controller
{
    protected $OperatorRepository, $VehicleRepository, $OrderRepository,$WalletTransactionRepository,$WalletRepository;


    public function __construct(
        OperatorRepository $OperatorRepository,
        VehicleRepository $VehicleRepository,
        OrderRepository $OrderRepository,
        WalletRepository $walletRepository,
        WalletTransactionRepository $WalletTransactionRepository
    )
    {
        $this->OperatorRepository = $OperatorRepository;
        $this->VehicleRepository = $VehicleRepository;
        $this->OrderRepository = $OrderRepository;
        $this->WalletRepository = $walletRepository;
        $this->WalletTransactionRepository = $WalletTransactionRepository;
    }

    //auth
    public function send_otp(Request $request)
    {
        return $this->OperatorRepository->send_otp($request);
    }

    public function login(Request $request)
    {
        return $this->OperatorRepository->login($request);
    }

    public function logout(Request $request)
    {
        return $this->OperatorRepository->logout($request);
    }

    public function profile(Request $request)
    {
        return $this->OperatorRepository->profile($request);
    }

    public function change_password(Request $request)
    {
        return $this->OperatorRepository->change_password($request);
    }

    public function update_profile(Request $request)
    {
        return $this->OperatorRepository->update_profile($request);
    }

    public function send_otp_to_update_phone(Request $request)
    {
        return $this->OperatorRepository->send_otp_to_update_phone($request);
    }

    public function update_phone(Request $request)
    {
        return $this->OperatorRepository->update_phone($request);
    }

    public function update_status(Request $request)
    {
        return $this->OperatorRepository->update_status($request);
    }

    //vehicle
    public function add_vehicle(Request $request)
    {
        return $this->VehicleRepository->add_vehicle($request);
    }


    public function add_operator(OperatorRequest $request)
    {
        return $this->OperatorRepository->add_operator($request);
    }

    //orders
    public function new_order(Request $request)
    {
        return $this->OrderRepository->new_order($request);
    }

    public function accept_order(Request $request)
    {
        return $this->OrderRepository->accept_order($request);
    }

    public function cancel_order_request(Request $request)
    {
        return $this->OrderRepository->cancel_order_request($request);
    }

    public function accept_cancel_order_request(Request $request)
    {
        return $this->OrderRepository->accept_cancel_order_request($request);
    }

    public function driver_orders(Request $request)
    {
        return $this->OrderRepository->driver_orders($request);
    }

    public function driver_orders_history(Request $request)
    {
        return $this->OrderRepository->driver_orders_history($request);
    }

    public function driver_orders_failed(Request $request)
    {
        return $this->OrderRepository->driver_orders_failed($request);
    }

    public function update_order_status(Request $request)
    {
        return $this->OrderRepository->update_status($request);
    }

    public function send_order_otp(Request $request)
    {
        return $this->OrderRepository->send_order_otp($request);
    }

    //bank details
    public function new_bank_details(Request $request)
    {
        return $this->OperatorRepository->new_bank_details($request);
    }

    public function bank_details()
    {
        return $this->OperatorRepository->bank_details();
    }

    //wallet
    public function add_wallet_balance(Request $request)
    {
        return $this->WalletRepository->add_wallet_balance($request);
    }

    public function wallet_details()
    {
        return $this->WalletRepository->wallet_details();
    }

    public function wallet_transactions(Request $request)
    {
        return $this->WalletTransactionRepository->get_data($request);
    }

    public function report_problem(Request $request)
    {
        return $this->OrderRepository->report_problem($request);

    }


    public function detect_location(Request $request)
    {
        return $this->OperatorRepository->detect_location($request);
    }

}
