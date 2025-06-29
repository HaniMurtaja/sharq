<?php

namespace App\Http\Controllers\Admin;

use Exception;



use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReasonRequest;
use App\Models\Reason;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;


class ReasonsController  extends Controller
{


    public function cancelReasons () {
        return view('admin.pages.cancellReasons.index');
    }



    public function reasonList(Request $request)
    {
        // abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');

        $columns = ['id', 'name'];

        $totalData = Reason::count();
        $totalFiltered = $totalData;

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');

        $order = $columns[$orderColumn] ?? $columns[0];

        if (empty($request->input('search.value'))) {
            $countries = Reason::offset($start)
                ->limit($limit)
                ->orderBy($order, $orderDir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $countries = Reason::where('name', 'LIKE', "%{$search}%")

                ->offset($start)
                ->limit($limit)
                ->orderBy($order, $orderDir)
                ->get();

            $totalFiltered = Reason::where('name', 'LIKE', "%{$search}%")

                ->count();
        }

        $data = [];
        if (!empty($countries)) {
            foreach ($countries as $country) {
                $nestedData['id'] = $country->id;
                $nestedData['name'] = $country->name;
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

    public function editReason($id = null)
    {
        // abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');

        $reason = Reason::findOrFail($id);
        return response()->json(['reason' => $reason]);
    }

    public function updateReason(ReasonRequest $request, $id)
    {
        // abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');

        $reason = Reason::findOrFail($id);
        $reason->update([
            'name' => $request['name'],

        ]);
    }


    public function saveReason(ReasonRequest $request)
    {
        // abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');

        // dd($request->all());
        try {

            if ($request->reason_id) {
                $this->updateReason($request, $request->reason_id);
            } else {
                Reason::create([
                    'name' => $request->name,
                ]);
            }

            return response()->json(['message' => 'Reason saved successfully']);
        } catch (Exception $e) {

            return response()->json(['error' => 'Failed to save reason'], 500);
        }
    }

    public function deleteReason($id)
    {
        // abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');

        $reason = Reason::findOrFail($id);
        $reason->delete();
        return response()->json('reason deleted successfully');
    }





   
}
