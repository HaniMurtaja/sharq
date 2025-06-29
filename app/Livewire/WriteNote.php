<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\Operator;
use App\Models\OrderLog;
use App\Enum\OrderStatus;
use App\Models\OrderNote;
use App\Enum\DriverStatus;
use App\Models\OrderDriver;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use LivewireUI\Modal\ModalComponent;
use App\Repositories\FirebaseRepository;
use App\Http\Resources\Api\OrderResource;

use  App\Http\Services\NotificationService;

class WriteNote extends ModalComponent
{
    public $message = "";
    public $order_id;
  

   
   
    protected $notificationService;
 
    public function __construct()
    {
        

    }
    public function delete()
    {

        $this->reset('message');
    }

    public function getValidationAttributes()
    {
        return [
            'message' => 'message',
        ];
    }

    public function getRules()
    {
        return [
           'message' => 'required|string',
        ];
    }

  
    public function render()
    {
        
        return view('livewire.write-note');
    }

    public function writeNote ( NotificationService $notificationService = null)
    {
       
        $this->notificationService = $notificationService;
        $this->validate();
//    dd($this->order_id);
        $driver = OrderDriver::where('order_id', $this->order_id)->orderBy('created_at', 'desc')->first()?->driver;
        OrderNote::create([
            'order_id' => $this->order_id,
            'driver_id' => $driver?->id,
            'message' => $this->message
        ]);

        try {
         
            if ($driver) {
                $title = 'Notes from '. auth()->user()->full_name;
                $body = $this->message;
             
                $this->notificationService->sendOrderNotifications($driver->id, $title, $body, $this->order_id, 'orders', '');
            }
          
          
        } catch (\Exception $e) {
          
            // Log::info($e);
        }

        $this->closeModal();
    }
}
