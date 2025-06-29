<?php

namespace App\Http\Controllers\Admin;

use Exception;

use App\Models\Group;
use App\Models\Shift;
use Nnjeim\World\World;
use App\Models\Operator;
use Illuminate\Http\Request;
use App\Models\GroupCondition;
use App\Models\OperatorDetail;
use App\Http\Requests\GroupRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ShiftRequest;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\OperatorRequest;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\EditOperatorRequest;
use App\Http\Requests\VehicleRequest;
use App\Models\Vehicle;
use App\Settings\GeneralSettings;

class VehicleController  extends Controller
{
   public function vehicleList(Request $request)
   {
      abort_unless(auth()->user()->hasPermissionTo('controle_vehicle'), 403, 'You do not have permission to view this page.');

      $columns = ['id', 'name'];

      $totalData = Vehicle::count();
      $totalFiltered = $totalData;

      $limit = $request->input('length', 10);
      $start = $request->input('start', 0);
      $orderColumn = $request->input('order.0.column', 0);
      $orderDir = $request->input('order.0.dir', 'asc');

      $order = $columns[$orderColumn] ?? $columns[0];

      if (empty($request->input('search.value'))) {
         $vehicles = Vehicle::offset($start)
            ->limit($limit)
            ->orderBy($order, $orderDir)
            ->get();
      } else {
         $search = $request->input('search.value');

         $vehicles = Vehicle::where('name', 'LIKE', "%{$search}%")

            ->offset($start)
            ->limit($limit)
            ->orderBy($order, $orderDir)
            ->get();

         $totalFiltered = Vehicle::where('name', 'LIKE', "%{$search}%")

            ->count();
      }

      $data = [];
      if (!empty($vehicles)) {
         foreach ($vehicles as $vehicle) {
            $nestedData['id'] = $vehicle->id;
            $nestedData['name'] = $vehicle->name;
            $data[] = $nestedData;
         }
      }

      $json_data = [
         "draw" => intval($request->input('draw')),
         "recordsTotal" => intval($totalData),
         "recordsFiltered" => intval($totalFiltered),
         "data" => $data
      ];

      return response()->json($json_data);
   }



   public function save(VehicleRequest $request)
   {

      abort_unless(auth()->user()->hasPermissionTo('controle_vehicle'), 403, 'You do not have permission to view this page.');
      try {
         if ($request->vehicle_id) {
            $this->edit($request, $request->vehicle_id);
         } else {
            $vehicle = Vehicle::create($request->all());
            if ($request['vehicle_image']) {
               $vehicle->addMedia($request['vehicle_image'])->toMediaCollection('vehicle_image');
            }

            if ($request['id_card_image']) {
               $vehicle->addMedia($request['id_card_image'])->toMediaCollection('id_card_image');
            }
         }

         return response()->json(['message' => 'Vehicle saved successfully']);
      } catch (Exception $e) {

         return response()->json(['error' => 'Failed to save vehicle'], 500);
      }
   }

   public function update($id)
   {
      abort_unless(auth()->user()->hasPermissionTo('controle_vehicle'), 403, 'You do not have permission to view this page.');

      $vehicle = Vehicle::findOrFail($id);
      $vehicle_image_url = $vehicle->getFirstMediaUrl('vehicle_image');
      $id_card_image_url = $vehicle->getFirstMediaUrl('id_card_image');
      return response()->json(['vehicle' => $vehicle, 'vehicle_image_url' => $vehicle_image_url, 'id_card_image_url' => $id_card_image_url]);
   }

   public function edit(VehicleRequest $request, $id)
   {
      abort_unless(auth()->user()->hasPermissionTo('controle_vehicle'), 403, 'You do not have permission to view this page.');

      $vehicle = Vehicle::findOrFail($id);
      $vehicle->update($request->all());


      if ($request['vehicle_image']) {
         $vehicle->clearMediaCollection('vehicle_image');

         $vehicle->addMedia($request['vehicle_image'])->toMediaCollection('vehicle_image');
      }

      if ($request['id_card_image']) {
         $vehicle->clearMediaCollection('id_card_image');

         $vehicle->addMedia($request['id_card_image'])->toMediaCollection('id_card_image');
      }
   }

   public function delete($id)
   {
      abort_unless(auth()->user()->hasPermissionTo('controle_vehicle'), 403, 'You do not have permission to view this page.');

      $vehicle = Vehicle::findOrFail($id);
      $vehicle->delete();
      return response()->json('Vehicle deleted successfully');
   }
}
