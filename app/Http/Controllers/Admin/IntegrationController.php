<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\IntegrationRequest;
use App\Http\Requests\WebHookRequest;
use App\Models\IntegrationCompany;
use App\Models\WebHook;
use Exception;
use Illuminate\Http\Request;

class IntegrationController extends Controller
{
    public function integrationList(Request $request)
    {
        // dd(99);
        abort_unless(auth()->user()->hasPermissionTo('controle_integration'), 403, 'You do not have permission to view this page.');

        $columns = ['id', 'name'];

        $totalData = IntegrationCompany::count();
        // dd($totalData);
        $totalFiltered = $totalData;

        $limit       = $request->input('length', 10);
        $start       = $request->input('start', 0);
        $orderColumn = $request->input('order.0.column', 0);
        // $orderDir = $request->input('order.0.dir', 'asc');

        $order = $columns[$orderColumn] ?? $columns[0];

        if (empty($request->input('search.value'))) {
            $webhooks = IntegrationCompany::orderBy('created_at', 'desc')
                ->offset($start)
                ->limit($limit)

                ->get();
        } else {
            $search = $request->input('search.value');

            $webhooks = IntegrationCompany::where('name', 'LIKE', "%{$search}%")

                ->offset($start)
                ->limit($limit)
                ->orderBy('created_at', 'desc')
                ->get();

            $totalFiltered = WebHook::where('name', 'LIKE', "%{$search}%")

                ->count();
        }

        $data = [];
        if (! empty($webhooks)) {
            foreach ($webhooks as $webhook) {
                $nestedData['id']   = $webhook->id;
                $nestedData['name'] = $webhook->name;
                $data[]             = $nestedData;
            }
        }

        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
        ];

        return response()->json($json_data);
    }

    public function webhookList(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_integration'), 403, 'You do not have permission to view this page.');
        // $webhooksCount = WebHook::select('integration_company_id')
        // ->selectRaw('COUNT(*) as total')
        // ->groupBy('integration_company_id')
        // ->get();
        // dd($webhooksCount);
        $columns = ['id', 'name', 'company'];

        $totalData     = WebHook::count();
        $totalFiltered = $totalData;

        $limit       = $request->input('length', 10);
        $start       = $request->input('start', 0);
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir    = $request->input('order.0.dir', 'asc');

        $order = $columns[$orderColumn] ?? $columns[0];

        if (empty($request->input('search.value'))) {
            $webhooks = WebHook::offset($start)
                ->limit($limit)
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $search = $request->input('search.value');

            $webhooks = WebHook::where('name', 'LIKE', "%{$search}%")
                ->orWhereHas('company', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}");
                })
                ->offset($start)
                ->limit($limit)
                ->orderBy('created_at', 'desc')
                ->get();

            $totalFiltered = WebHook::where('name', 'LIKE', "%{$search}%")
                ->orWhereHas('company', function ($q) use ($search) {
                    $q->where('name', 'LIKE', "%{$search}");
                })
                ->count();
        }

        $data = [];
        if (! empty($webhooks)) {
            foreach ($webhooks as $webhook) {
                $nestedData['id']      = $webhook->id;
                $nestedData['name']    = $webhook->name;
                $nestedData['company'] = $webhook->company?->name;
                $data[]                = $nestedData;
            }
        }

        $json_data = [
            "draw"            => intval($request->input('draw')),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalFiltered),
            "data"            => $data,
        ];

        return response()->json($json_data);
    }

    public function save(IntegrationRequest $request)
    {
        // dd($request->all());
        abort_unless(auth()->user()->hasPermissionTo('controle_integration'), 403, 'You do not have permission to view this page.');
        try {
            if ($request->integration_id) {
                $this->edit($request, $request->integration_id);
            } else {
                $integration = IntegrationCompany::create([
                    'name'              => $request->name,
                    'has_cancel_reason' => $request->has_cancel_reason ?? 0,
                    'client_type'       => $request->client_type ?? 0,
                    'otp_awb'           => $request->otp_awb ?? 0,

                ]);
            }

            return response()->json(['message' => 'Integratio saved successfully']);
        } catch (Exception $e) {

            return response()->json(['error' => 'Failed to save integration'], 500);
        }
    }

    public function saveWebhook(WebHookRequest $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_integration'), 403, 'You do not have permission to view this page.');

        try {
            if ($request->webhook_id) {
                $this->editWebhook($request, $request->webhook_id);
            } else {
                $company     = IntegrationCompany::findOrFail($request->integration_company_id);
                $integration = WebHook::create([
                    'name'                   => $request->name_webhook,
                    'integration_company_id' => $request->integration_company_id,
                    'url'                    => $request->url,
                    'type'                   => $request->type,
                    'format'                 => $request->format,
                    'client_type'            => $company->client_type,
                    'otp_awb'                => $request->otp_awb ?? 0,

                ]);
            }

            return response()->json(['message' => 'Integratio saved successfully']);
        } catch (Exception $e) {

            return response()->json(['error' => 'Failed to save integration'], 500);
        }
    }

    public function update($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_integration'), 403, 'You do not have permission to view this page.');
        $integration = IntegrationCompany::findOrFail($id);

        return response()->json(['integration' => $integration]);
    }

    public function updateWebhook($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_integration'), 403, 'You do not have permission to view this page.');
        $integration = WebHook::findOrFail($id);

        return response()->json(['integration' => $integration]);
    }

    public function edit(IntegrationRequest $request, $id)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_integration'), 403, 'You do not have permission to view this page.');
        $integration = IntegrationCompany::findOrFail($id);
        $integration->update([
            'name'              => $request->name,
            'has_cancel_reason' => $request->has_cancel_reason ?? 0,
            'client_type'       => $request->client_type,
            'otp_awb'           => $request->otp_awb ?? 0,
        ]);

        $integration->webhooks()->update([
            'client_type' => $integration->client_type,
        ]);
    }

    public function editWebhook(WebHookRequest $request, $id)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_integration'), 403, 'You do not have permission to view this page.');

        $integration = WebHook::findOrFail($id);
        $integration->update([
            'name'                   => $request->name_webhook,
            'integration_company_id' => $request->integration_company_id,
            'url'                    => $request->url,
            'type'                   => $request->type,
            'format'                 => $request->format,
        ]);
    }

    public function delete($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_integration'), 403, 'You do not have permission to view this page.');

        $integration = IntegrationCompany::findOrFail($id);
        $integration->delete();
        return response()->json('Integration deleted successfully');
    }

    public function deleteWebhook($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_integration'), 403, 'You do not have permission to view this page.');

        $integration = WebHook::findOrFail($id);
        $integration->delete();
        return response()->json('Integration deleted successfully');
    }
}
