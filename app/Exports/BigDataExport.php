<?php

namespace App\Exports;

use App\Enum\UserRole;
use App\Http\Controllers\Admin\DispatcherController;
use App\Models\Order;
use App\Models\ExportedOrders;
use App\Models\UserCitys;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BigDataExport implements FromQuery, WithMapping, WithHeadings, WithChunkReading, ShouldQueue
{
    use Exportable;

    protected array $filters;
    protected  $userRole;
    protected  $userId;
    protected  $branchId;

    public function __construct(array $filters = [], $userRole, $userId, $branchId)
    {
        $this->filters = $filters;
        $this->userRole = $userRole;
        $this->userId = $userId;
        $this->branchId = $branchId;
    }

    public function query()
    {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        $query = ExportedOrders::query()->orderByDesc('order_created_at');

        if (@$this->userRole == UserRole::CLIENT) {
            $query->where('ingr_shop_id', $this->userId);
        }

        if (@$this->userRole == UserRole::BRANCH) {
            $query->where('ingr_branch_id', $this->branchId);
        }
        if (@$this->userRole == UserRole::DISPATCHER) {
            // Get city IDs for the current user (with caching)
            $cacheKey = "user_cities_{$this->userId}";

            $city_ids = Cache::remember($cacheKey, now()->addHours(1), function () use ($userId) {
                return UserCitys::where('user_id', $this->userId)
                    ->pluck('city_id')
                    ->toArray();
            });

            if (empty($city_ids)) {
                return;
            }

            if ($query !== null) {
                $query->where(function ($query) use ($city_ids) {
                    $query->whereIn('city', $city_ids)
                        ->orWhereNull('city');
                });
            }
        }

        if (!empty($this->filters['id'])) $query->where('id', $this->filters['id']);
        if (!empty($this->filters['client_order_id_string'])) $query->where('client_order_id_string', $this->filters['client_order_id_string']);
        if (!empty($this->filters['status_ids'])) $query->whereIn('status', $this->filters['status_ids']);
        if (!empty($this->filters['city_id'])) $query->where('city', $this->filters['city_id']);
        if (!empty($this->filters['driver_id'])) $query->where('driver_id', $this->filters['driver_id']);
        if (!empty($this->filters['client_id'])) $query->where('ingr_shop_id', $this->filters['client_id']);
        if (!empty($this->filters['ingr_branch_id'])) $query->where('ingr_branch_id', $this->filters['ingr_branch_id']);
        if (!empty($this->filters['customer_phone'])) $query->where('customer_phone', 'like', '%' . $this->filters['customer_phone'] . '%');
        if (!empty($this->filters['customer_name'])) $query->where('customer_name', 'like', '%' . $this->filters['customer_name'] . '%');

        if (!empty($this->filters['fromtime'])) {
            $column = $this->filters['datesearch'] ?? 'order_created_at';
            $query->where($column, '>=', date('Y-m-d H:i:s', strtotime($this->filters['fromtime'])));
        }

        if (!empty($this->filters['totime'])) {
            $column = $this->filters['datesearch'] ?? 'order_created_at';
            $query->where($column, '<=', date('Y-m-d H:i:s', strtotime($this->filters['totime'])));
        }


        return $query;
    }
    public function map($row): array
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);
        try {
            //\Log::info("Exporting order ID: {$row->id}");

            return [
                $row->order_id,
                $row->OrderNumber,
                $row->customer_name ,
                $row->customer_phone,
                $row->value ?? 0,
                $row->service_fees ?? 0,
                $row->total ?? 0,
                $row->status ,
                $row->payment_type ,
                $row->driver_id,
                $row->driver_name,
                $row->client_account,
                @$row->ingr_shop_id,
                @$row->shop_name,
                @$row->branch_id,
                @$row->branch_name,
                @$row->city,
                @$row->cancel_reason,
                @$row->order_created_at,
                @$row->driver_assigned_at,
                @$row->arrived_to_pickup_time,
                @$row->picked_up_time,
                @$row->arrived_to_dropoff_time,
                @$row->delivered_at,
                // @$row->pickup_distance,
                // @$row->delivery_distance,
                // @$row->pickup_duration,
                // @$row->delivery_duration,
            ];
        } catch (\Throwable $e) {
            \Log::error("❌ Failed exporting ID {$row->id}: " . $e->getMessage());
            return [
                $row->id,
                'ERROR',
            ];
        }
    }


    public function headings(): array
    {
        return [
            'Order ID',
            'Order Number',
            'Customer Name',
            'Customer Phone',
            'Order Value',
            'Service Fees',
            'Total Value',
            'Order Status',
            'Payment Type',
            'Driver ID',
            'Driver Name',
            'Client Account Number',
            'Client ID',
            'Client Name',
            'Branch ID',
            'Branch Name',
            'City',
            'Cancel Reason',
            'Created At',
            'Driver Assigned At',
            'Arrived to Pickup',
            'Picked Up',
            'Arrived to Dropoff',
            'Delivered At',
            // 'Pickup Distance',
            // 'Delivery Distance',
            // 'Pickup Duration',
            // 'Delivery Duration',
        ];
    }

    public function chunkSize(): int
    {
        return 200;
    }
    public function batchSize(): int
    {
        return 200;
    }
}
