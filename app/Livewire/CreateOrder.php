<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Client;
use App\Models\Vehicle;
use App\Rules\KSAPhoneRule;
use App\Models\ClientBranches;
use LivewireUI\Modal\ModalComponent;


class CreateOrder extends ModalComponent
{
    public $lat = 24.7136;
    public $lng = 46.6753;




    public $customer_name;
    public $customer_phone;
    public $instructions;
    public $client_id = 0;
    public $branch_id = 0;
    public $client_order_id;
    public $items_no;
    public $order_value;
    public $payment_method;
    public $proof_action = "none";
    public $vehicle_id = 0;
    public $pickup_instructions;
    public $order_details;
    public $preperation_time = 0;
    public $date_time;
    public $arive_in = 1;
    public $driver_in;
    public $service_fees = 5;

    public $clients;
    public $branches;
    public $vehicles;


    public $current_step = 1;
    public $total_steps = 3;


    public function getValidationAttributes()
    {
        return [
            'client_order_id' => 'client order ID',
            'order_value' => 'order value',
            'payment_method' => 'payment method',
            'preperation_time' => 'preparation time',
            'branch_id' => 'Branch',
            'client_id' => 'Client',
            'customer_phone' => 'customer phone',
            'customer_name' => 'customer name',
            'date_time' => 'date and time',
            'order_details' => 'order details',
            'instructions' => 'instructions',
            'pickup_instructions' => 'pickup instructions',
            'proof_action' => 'proof of action',
            'items_no' => 'number of items',
            'vehicle_id' => 'vehicle',
            'arive_in' => 'arrival time',
            'service_fees' => 'service fees',
            'driver_in' => 'driver time',
        ];
    }


    public function getRules()
    {
        $validates = [];
        if ($this->current_step == 1) {
            $validates = [
                'instructions' => 'nullable|string',
                'customer_phone' => [
                    'required',
                    new KSAPhoneRule()
                ],
                'customer_name' => 'required|string',
            ];
        }

        if ($this->current_step == 2) {
            $validates = [
                'branch_id' => 'required|exists:client_branches,id',
                'client_id' => 'required|exists:users,id',
                'client_order_id' => 'nullable',
                'items_no' => 'nullable|numeric',
                'order_value' => 'nullable|numeric',
                'payment_method' => 'nullable|integer',
                'proof_action' => 'nullable|string',
                'vehicle_id' => 'nullable|exists:vehicles,id',
                'order_details' => 'nullable|string',
                'pickup_instructions' => 'nullable|string',
            ];
        }

        if ($this->current_step == 3) {
            $validates = [
                'preperation_time' => 'nullable|integer|min:0',
                'date_time' => 'nullable|date',
                'arive_in' => 'nullable|integer|min:1',
                'service_fees' => 'nullable|numeric|min:0',
                'driver_in' => 'nullable|integer',
            ];
        }
        return $validates;
    }


    public function render()
    {
        $this->clients = Client::all();
        $this->branches = ClientBranches::where('client_id', $this->client_id)->get();
        $this->vehicles = Vehicle::all();
        return view('livewire.create-order', [
            'clients' => $this->clients,
            'branches' => $this->branches,
            'vehicles' => $this->vehicles
        ]);
    }


    public function decrementStep()
    {
        if ($this->current_step > 1) {
            $this->current_step--;
        }
    }
    public function incrementStep()
    {
        // dd(44);
        if ($this->current_step < $this->total_steps) {
            $this->validate();
            $this->current_step++;
        }
        return;
    }


    public function save()
    {
        $this->validate();
        Order::create([
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,

            'instruction' => $this->instructions,
            'ingr_shop_id' => $this->client_id,
            'ingr_branch_id' => $this->branch_id,
            'client_order_id' => $this->client_order_id,
            'items_no' => $this->items_no,
            'value' => $this->order_value,
            'payment_type' => $this->payment_method,
            'proof_of_action' =>  $this->proof_action,
            'vehicle_id' => $this->vehicle_id,
            'pickup_instruction' =>  $this->pickup_instructions,
            'details' => $this->order_details,
            'preparation_time' => $this->preperation_time,
            'deliver_at' => $this->date_time,
            'driver_arrive_time' =>  $this->arive_in,
            'delivered_in' => $this->driver_in,
            'service_fees' => $this->service_fees,


        ]);
        $this->closeModal();
    }
}
