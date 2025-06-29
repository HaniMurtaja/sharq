<?php

namespace App\Models;

use App\Enum\OrderStatus;
use App\Enum\PaymentType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // This model uses an observer. //

    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'status'             => OrderStatus::class,
        'payment_type'       => PaymentType::class,
        'additional_details' => 'array',
    ];
    public function getOrderNumberAttribute(): string
    {
        return $this->client_order_id_string ?? $this->client_order_id ?? $this->id;
    }

    public function getPdfUrlrAttribute(): string
    {
        return asset($this->invoice_url);
    }

    public function cancel_reason()
    {
        return $this->belongsTo(Reason::class, 'reason_id');
    }
    public function DriverData2()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
    public function DriverDataSearch()
    {
        return $this->belongsTo(User::class, 'driver_id')->withDefault();
    }

    public function OperatorDetail()
    {
        return $this->belongsTo(OperatorDetail::class, 'driver_id', 'operator_id');
    }

    public function shop()
    {
        return $this->belongsTo(Client::class, 'ingr_shop_id', 'id');
    }

    public function branch()
    {
        return $this->belongsTo(ClientBranches::class, 'ingr_branch_id', 'id');
    }

    public function branchIntegration()
    {
        return $this->belongsTo(ClientBranches::class, 'pickup_id');
    }

    public function ClientDetail()
    {
        return $this->belongsTo(ClientDetail::class, 'ingr_shop_id', 'user_id');
    }

    public function drivers()
    {
        return $this->hasMany(OrderDriver::class);
    }

    public function DriverData()
    {
        return $this->hasMany(OperatorDetail::class, 'operator_id', 'driver_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function driver()
    {
        return $this->hasOne(OrderDriver::class);
    }
    public function city()
    {
        return $this->belongsTo(City::class, 'city');
    }

    public function cityData(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(City::class, 'city');
    }

    public function orderLogs()
    {
        return $this->hasMany(OrderLog::class, 'order_id');
    }

    public function getFullInvoiceUrlAttribute()
    {
        $invoice = @$this->invoice_url;

        return $invoice ? "https://alshrouqdelivery.b-cdn.net/" . $invoice : null;
        // return $this->invoice_url ? url('storage/'.$this->invoice_url) : '';
    }
    //SELECT `id`, `pickup_lat`, `pickup_lng`, `pickup_id`, `client_order_id`, `value`, `payment_type`, `preparation_time`, `lat`, `lng`, `address`, `city`, `customer_phone`, `customer_name`, `deliver_at`, `details`, `pickup_poa`, `dropoff_poa`, `ingr_shop_id`, `ingr_branch_id`, `ingr_shop_name`, `ingr_branch_name`, `ingr_branch_lat`, `ingr_branch_lng`, `ingr_branch_phone`, `instruction`, `pickup_instruction`, `proof_of_action`, `items_no`, `driver_arrive_time`, `service_fees`, `delivered_in`, `created_at`, `updated_at`, `vehicle_id`, `status`, `distance`, `jop_id`, `integration_id`, `client_order_id_string`, `driver_id`, `additional_details`, `delivered_at`, `integration_token`, `driver_assigned_at`, `assign_try_count`, `otp`, `otp_sent_at` FROM `orders` WHERE 1
    public function ShopDetail(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'ingr_shop_id');
    }
    public static function getBusinessHours()
    {

        $settings = new \App\Settings\GeneralSettings();

        $startTime        = Carbon::createFromFormat('H:i', $settings->business_hours['start_time']);
        $endTime          = Carbon::createFromFormat('H:i', $settings->business_hours['end_time']);
        $shiftEndTomorrow = $settings->shift_end_tomorrow;

        if ($shiftEndTomorrow || $endTime->lessThan($startTime)) {
            $startDateTime = Carbon::today('Asia/Riyadh')->setTimeFrom($startTime);
            $endDateTime   = Carbon::tomorrow('Asia/Riyadh')->setTimeFrom($endTime);
        } else {
            $startDateTime = Carbon::today('Asia/Riyadh')->setTimeFrom($startTime);
            $endDateTime   = Carbon::today('Asia/Riyadh')->setTimeFrom($endTime);
        }
        $data = [
            'startDateTime' => $startDateTime,
            'endDateTime'   => $endDateTime,
            'now'           => Carbon::now('Asia/Riyadh'),
        ];
        // dd( $data);
        return $data;
    }
}
