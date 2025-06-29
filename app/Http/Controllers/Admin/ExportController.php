<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\City;
use App\Enum\UserRole;
use App\Models\Client;
use App\Models\Operator;
use App\Exports\BigDataExport;
use App\Exports\OrderDashboardExport;
use Illuminate\Http\Request;
use App\Models\ClientBranches;
use App\Http\Controllers\Controller;
use App\Jobs\ExportCustomerInvoicesJob;
use App\Models\ExportedOrders;
use App\Models\ExportLog;
use App\Models\UserCitys;
use App\Traits\FileHandler;
use Illuminate\Support\Facades\Storage;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Bus;

class ExportController extends Controller
{
    use FileHandler;

    public function GetOrders(Request $request)
    {
        //abort_unless(auth()->user()->hasPermissionTo('previous_orders_basic_view'), 403, 'You do not have permission to view this page.');
        $clientsQuery = Client::select('id', 'first_name', 'last_name');
        $driversQuery = Operator::select('id', 'first_name', 'last_name');
        $getClientBranchesQuery = ClientBranches::orderBy('created_at', 'desc');
        $itemsQuery = ExportLog::orderbyDEsc('id');
        $citysQuery = City::query();





        $userId = auth()->id();
        $user = auth()->user();
        if ($user->user_role === UserRole::DISPATCHER) {
            $cacheKey = "user_cities_{$userId}";

            $city_ids = Cache::remember($cacheKey, now()->addHours(1), function () use ($userId) {
                return UserCitys::where('user_id', $userId)
                    ->pluck('city_id')
                    ->toArray();
            });


            if (empty($city_ids)) {
                return;
            }


            $citysQuery->whereIn('id', $city_ids);

            $clientsQuery->whereHas('client', function ($query) use ($city_ids) {
                $query->whereIn('city_id', $city_ids);
            });




            $getClientBranchesQuery->whereIn('city_id', $city_ids);

            $driversQuery->whereHas('cities', function ($q) use ($city_ids) {
                $q->whereIn('city_id', $city_ids);
            });
        }



        if ($user->user_role === UserRole::ADMIN && $user->country_id) {
            $countryId = $user->country_id;


            $citysQuery->where('country_id', $user->country_id);

            $clientsQuery->whereHas('client.city', function ($query) use ($countryId) {
                $query->where(function ($q) use ($countryId) {
                    $q->where('country_id', $countryId);
                });
            });




            $getClientBranchesQuery->whereHas('city', function ($query) use ($countryId) {
                $query->where('country_id', $countryId);
            });

            $driversQuery->whereHas('cities.city', function ($q) use ($countryId) {
                $q->where('country_id', $countryId);
            });


            $itemsQuery->where('country_id', $countryId);
        }








        if (auth()->user()->user_role == UserRole::CLIENT) {
            $getClientBranchesQuery->where('client_id', auth()->user()->id);
            $clientsQuery = [];
            $driversQuery = [];
        }
        if (auth()->user()->user_role == UserRole::BRANCH) {
            $getClientBranchesQuery->where('client_id', 0);
            $clientsQuery = [];
            $driversQuery = [];
        }
        $clients = is_array($clientsQuery) ? [] : $clientsQuery->get();
        $drivers = is_array($driversQuery) ? [] : $driversQuery->get();
        $getClientBranches = $getClientBranchesQuery->pluck('name', 'id');
        if ($request->export != '') {
            return $this->exportCustomerInvoices($request->export);
        }
        $items = $itemsQuery->paginate(10);
        $citys = $citysQuery->pluck('name', 'id');

        return view('admin.pages.exports.orders.index', compact(['items', 'clients', 'drivers', 'getClientBranches',  'citys']));
    }
    public function getData()
    {
        $query = \App\Models\ExportedOrders::orderByDesc('order_created_at')->paginate(20);
        return  $query;
    }
    public function exportCustomerInvoices($exportType)
    {
        if ($exportType !== 'xlsx') {
            abort(400, 'Invalid export type.');
        }

        $filename = 'orders_' . now()->format('Ymd_His') . '.xlsx';
        $exportPath = storage_path("app/public/exports/{$filename}");


        $exportsDir = storage_path('app/public/exports');
        if (!is_dir($exportsDir)) {
            mkdir($exportsDir, 0777, true);
        }
        $log = ExportLog::create([
            'user_id'   => auth()->id(),
            'file_name' => $filename,
            'file_path' => "exports/{$filename}",
            'country_id' => auth()->user()->country_id,
        ]);

        // (new \Rap2hpoutre\FastExcel\FastExcel($itemsQuery->cursor()->getIterator()))
        //     ->export($exportPath);
        $user = auth()->user();

        $filters = array_merge(
            request()->all(),
            [
                'user_role' => $user->user_role,
                'user_id' => $user->id,
                'branch_id' => $user->branch_id,

            ]
        );
        // dd($filters['user_role'] ,'g');

        Bus::chain([
            new ExportCustomerInvoicesJob($filters, $exportPath),
            new \App\Jobs\MarkExportAsReady($log->id)
        ])->dispatch();


        session()->flash('export_status', 'جاري تجهيز الملف... سيتم ظهوره هنا خلال لحظات.');

        return redirect()->route('export.GetOrders');
    }

    public function search($query, $filters)
    {
        //         $user_role = $filters['user_role'] ?? null;
        //         $user_id   = $filters['user_id']   ?? null;
        //         $branch_id = $filters['branch_id'] ?? null;
        // dd(@$filters->user_role);
        //         // Dispatcher role
        //         if ($user_role == UserRole::DISPATCHER) {
        //             $cacheKey = "user_cities_{$user_id}";
        //             $city_ids = Cache::remember($cacheKey, now()->addHours(1), function () use ($user_id) {
        //                 return \App\Models\UserCitys::where('user_id', $user_id)->pluck('city_id')->toArray();
        //             });

        //             if (empty($city_ids)) return;

        //             $query->where(function ($q) use ($city_ids) {
        //                 $q->whereIn('city_id', $city_ids)->orWhereNull('city_id');
        //             });
        //         }

        //         // Client/Branch
        //         if ($user_role == UserRole::CLIENT) {
        //             $query->where('ingr_shop_id', $user_id);
        //         } elseif ($user_role == UserRole::BRANCH) {
        //             $query->where('ingr_branch_id', $branch_id);
        //         }

        $map = [
            'id'                    => 'order_id',
            'client_order_id_string' => 'order_number',
            'client_order_id'       => 'order_number',
            'city_id'               => 'city_id',
            'driver_id'             => 'driver_id',
            'client_id'             => 'ingr_shop_id',
            'ingr_branch_id'        => 'ingr_branch_id',
            'assigned_by'           => 'assigned_by',
        ];
        foreach ($map as $reqKey => $dbColumn) {
            if (!empty($filters[$reqKey])) {
                $query->where($dbColumn, $filters[$reqKey]);
            }
        }

        if (!empty($filters['status_ids'])) {
            $query->whereIn('status_id', $filters['status_ids']);
        }

        if (!empty($filters['customer_phone'])) {
            $query->where('customer_phone', 'like', '%' . $filters['customer_phone'] . '%');
        }
        if (!empty($filters['customer_name'])) {
            $query->where('customer_name', 'like', '%' . $filters['customer_name'] . '%');
        }

        $column = $filters['datesearch'] ?? 'order_created_at';
        if (!empty($filters['fromtime'])) {
            $from = date("Y-m-d H:i:s", strtotime($filters['fromtime']));
            $query->where($column, ">=", $from);
        }
        if (!empty($filters['totime'])) {
            $to = date("Y-m-d H:i:s", strtotime($filters['totime']));
            $query->where($column, "<=", $to);
        }
    }


    public function exportCustomerInvoicesOld($exportType)
    {
        if ($exportType === 'xlsx') {
            $filters = request()->only([
                'id',
                'client_order_id',
                'status_ids',
                'city_id',
                'client_id',
                'fromtime',
                'totime'
            ]);

            $filename = 'orders_' . now()->format('Ymd_His') . '.xlsx';
            $path = 'exports/' . $filename;
            $log = ExportLog::create([
                'user_id' => auth()->id(),
                'file_name' => $filename,
                'file_path' => $path,
                'country_id' => auth()->user()->country_id,
            ]);
            ini_set('memory_limit', '-1');
            $user = auth()->user();

            (new \App\Exports\BigDataExport($filters, @$user->user_role, @$user->id, @$user->branch_id))->queue($path, 'public')->chain([
                (new \App\Jobs\MarkExportAsReady($log->id)),
            ]);
            session()->flash('export_status', 'جاري تجهيز الملف... سيتم ظهوره هنا خلال لحظات.');
            return redirect()->route('export.GetOrders');
        }
    }
}
