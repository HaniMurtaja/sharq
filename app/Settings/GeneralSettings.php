<?php

namespace App\Settings;

use Illuminate\Support\Arr;
use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{

    public array $account = [];
    public array $privacy_security = [];
    public array $api_settings = [];
    public array $vehicle_types = [];
    public array $operators = [];

    public array $auto_dispatch = [];

    public array $dispatcher_page = [];
    public array $dashboard_page = [];
    public array $services = [];
    public array $customer_messages = [];
    public array $eta = [];
    public array $announcements = [];
    public array $taxes = [];
    public array $dispatching = [];
    public array $payment_gateway = [];
    public array $foodics_connection = [];
    public array $business_hours = [];
    public int $time_multi_order_assign;
    public array $special_business_hours = [];
    public int $shift_end_tomorrow;
    public int $max_driver_orders;
    public array $max_distance_per_city;
    
    public array $auto_dispatch_per_city;


    public static function group(): string
    {
        return 'general';
    }
}
