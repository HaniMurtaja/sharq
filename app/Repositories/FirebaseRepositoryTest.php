<?php

namespace App\Repositories;

use Kreait\Firebase\Factory;
use App\Http\Resources\Admin\Dispatcher\FirbaseBranchesResource;
use App\Models\MapView;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class FirebaseRepositoryTest
{
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(app_path() . '/Http/Controllers/Api/firebase.json')
            ->withDatabaseUri('https://alshrouqexpress-97ebd-default-rtdb.firebaseio.com/');

        $this->database = $factory->createDatabase();
    }

    public function saveBranches(Collection $branches)
    {
        // dd($branches);
        foreach ($branches as $branch) {
            $data = (new FirbaseBranchesResource($branch))->toArray(request());

            $this->database
                ->getReference("branches/{$data['id']}")
                ->set($data);
        }
    }


    public function getBranchesByClientId($clientId)
    {


        $snapshot = $this->database->getReference('branches')->getSnapshot();

        if (!$snapshot->exists()) {
            return collect();
        }

        $allBranches = $snapshot->getValue();

        $filtered = collect($allBranches)->filter(function ($branch, $key) use ($clientId) {
            return isset($branch['client_id']) && $branch['client_id'] == $clientId;
        });


        return $filtered->map(function ($value, $key) {
            return [
                'firebase_key' => $key,
                'data' => $value,
            ];
        })->values();
    }


    public function getFilteredBranches($filters = [])
    {
        $snapshot = $this->database->getReference('branches')->getSnapshot();

        if (!$snapshot->exists()) {
            return collect();
        }

        $allBranches = $snapshot->getValue();

        $yesterday = \Carbon\Carbon::yesterday()->startOfDay();
        $today = \Carbon\Carbon::now()->endOfDay();

        return collect($allBranches)
            ->filter(function ($branch, $key) use ($filters, $yesterday, $today) {
                $clientMatch = !isset($filters['client_id']) || (isset($branch['client_id']) && $branch['client_id'] == $filters['client_id']);
                $idMatch = !isset($filters['id']) || (isset($branch['id']) && $branch['id'] == $filters['id']);
                $hasOrders = isset($branch['orders_count']) && $branch['orders_count'] !== 0;
                $hasLatLng = isset($branch['lat'], $branch['lng']) && $branch['lat'] !== null && $branch['lng'] !== null;


                $dateMatch = false;
                if (isset($branch['created_at'])) {
                    try {
                        $createdAt = \Carbon\Carbon::parse($branch['created_at']);
                        $dateMatch = $createdAt->between($yesterday, $today);
                    } catch (\Exception $e) {
                        $dateMatch = false;
                    }
                }

                return $clientMatch && $idMatch && $hasOrders && $hasLatLng && $dateMatch;
            })
            ->map(function ($branch, $key) {
                return [
                    'firebase_key' => $key,
                    'data' => $branch,
                ];
            })
            ->values();
    }



    public function saveMapData(int $orderId): void
    {
        // dd(8);
        try {
            $mapData = MapView::whereNotNull('order_lat')->where('order_id', $orderId)->get();

            if ($mapData->isNotEmpty()) {

                $grouped = $mapData->groupBy('driver_id');

                foreach ($grouped as $driverId => $driverOrders) {

                    $firebaseData = (new \App\Http\Resources\Maps\MapsResource($driverOrders))->resolve();

                    $this->database
                        ->getReference("map/{$orderId}")
                        ->set($firebaseData);
                }
            }
        } catch (\Throwable $e) {
            \Log::error("Failed to save map data for order #{$orderId}: " . $e->getMessage());
        }
    }

    public function deleteMapOrder(int $orderId): void
    {
        try {
            $this->database
                ->getReference("map/{$orderId}")
                ->remove();
        } catch (\Throwable $e) {
            \Log::error("Failed to delete map data for order #{$orderId}: " . $e->getMessage());
        }
    }




    public function getMapDataForTodayAndYesterday(?int $shopId = null, ?int $branchId = null): array
    {
        try {
            $snapshot = $this->database->getReference('map')->getSnapshot();

            if (!$snapshot->exists()) {
                return [];
            }

            $allMapData = $snapshot->getValue();
            $filteredData = [];

            $yesterday = \Carbon\Carbon::yesterday()->startOfDay();
            $today = \Carbon\Carbon::now()->endOfDay();

            foreach ($allMapData as $orderId => $mapEntry) {
                if (!isset($mapEntry['order_created_at'])) {
                    continue;
                }

                $orderCreatedAt = \Carbon\Carbon::parse($mapEntry['order_created_at']);


                if (!$orderCreatedAt->between($yesterday, $today)) {
                    continue;
                }


                if (
                    ($shopId !== null && (!isset($mapEntry['ingr_shop_id']) || $mapEntry['ingr_shop_id'] != $shopId)) ||
                    ($branchId !== null && (!isset($mapEntry['ingr_branch_id']) || $mapEntry['ingr_branch_id'] != $branchId))
                ) {
                    continue;
                }

                $filteredData[$orderId] = $mapEntry;
            }

            return $filteredData;
        } catch (\Throwable $e) {
            \Log::error("Failed to get filtered map data: " . $e->getMessage());
            return [];
        }
    }
}
