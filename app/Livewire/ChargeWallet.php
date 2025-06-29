<?php

    namespace App\Livewire;

    use App\Http\Resources\Api\OrderResource;
    use App\Models\Operator;
    use App\Models\OrderDriver;
    use App\Repositories\FirebaseRepository;
    use Illuminate\Support\Facades\App;
    use Illuminate\Support\Facades\Log;
    use App\Enum\DriverStatus;
use App\Enum\OrderStatus;
use Livewire\Attributes\Url;
    use App\Models\Order;
use App\Models\OrderLog;
use LivewireUI\Modal\ModalComponent;
   
    class ChargeWallet extends ModalComponent
    {
        public $client_id = "";
        public $amount = 0;
       
       

        public function updatedSearch()
        {
            $this->resetPage();
        }

        public function delete()
        {

            $this->reset('searchName');
        }

        public function getValidationAttributes()
        {
            return [
                'amount' => 'amount',
            ];
        }

        public function getRules()
        {
            return [
                'amount' => ['required', 'integer'],
            ];
        }

        public function mount($client_id)
        {
            $this->client_id = $client_id;
        }

        public function render()
        {
            
            return view('livewire.charge-wallet');
        }

       
    }
