<?php

namespace App\Repositories;

use App\Http\Services\NotificationService;
use Kreait\Firebase\Factory;
use Validator;
use Auth;

class FirebaseRepository
{
    public function __construct(NotificationService $notificationService) {
        $this->notificationService = $notificationService;
    }

    // public function save_driver_order($driver_id,$order)
    // {
    //     $factory = (new Factory)->withServiceAccount(app_path() . '/Http/Controllers/Api/firebase.json');
    //     $database = $factory->withDatabaseUri('https://alshrouqexpress-97ebd-default-rtdb.firebaseio.com/')->createDatabase();
    //     $new_order = $database->getReference('drivers/' . $driver_id.'/orders/new-order')->update($order);
    // }

    // public function delete_driver_order($driver_id)
    // {
    //     $factory = (new Factory)->withServiceAccount(app_path() . '/Http/Controllers/Api/firebase.json');
    //     $database = $factory->withDatabaseUri('https://alshrouqexpress-97ebd-default-rtdb.firebaseio.com/')->createDatabase();
    //     $new_order = $database->getReference('drivers/' . $driver_id.'/orders/new-order')->remove();
    // }


    public function save_driver_order($driver_id, $order)
    {


        $factory = (new Factory)->withServiceAccount(app_path() . '/Http/Controllers/Api/firebase.json');

        $database = $factory->withDatabaseUri('https://alshrouqexpress-97ebd-default-rtdb.firebaseio.com/')->createDatabase();

        $order_id = $order['order_id'];

        $new_order_ref = $database->getReference('drivers/' . $driver_id . '/orders/new-order/' . $order_id);


        $new_order_ref->set($order);

    }

    public function delete_driver_order($driver_id, $order_id)
    {
        $factory = (new Factory)->withServiceAccount(app_path() . '/Http/Controllers/Api/firebase.json');
        $database = $factory->withDatabaseUri('https://alshrouqexpress-97ebd-default-rtdb.firebaseio.com/')->createDatabase();

        $new_order_ref = $database->getReference('drivers/' . $driver_id . '/orders/new-order/' . $order_id);

        $new_order_ref->remove();
    }

    public function delete_all($driver_id)
    {
        $factory = (new Factory)->withServiceAccount(app_path() . '/Http/Controllers/Api/firebase.json');
        $database = $factory->withDatabaseUri('https://alshrouqexpress-97ebd-default-rtdb.firebaseio.com/')->createDatabase();

        $new_order_ref = $database->getReference('drivers/' . $driver_id . '/orders/new-order/');

        $new_order_ref->remove();
    }

    public function save_driver($driver_id, $driver)
    {
        $factory = (new Factory)->withServiceAccount(app_path() . '/Http/Controllers/Api/firebase.json');
        $database = $factory->withDatabaseUri('https://alshrouqexpress-97ebd-default-rtdb.firebaseio.com/')->createDatabase();
        $new_driver = $database->getReference('drivers/' . $driver_id . '/profile')->update($driver);
    }
}
