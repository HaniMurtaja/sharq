<?php

namespace App\Http\Controllers\Admin;

use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use App\Models\Client;

class FoodicsClientsController extends Controller
{

    public function index()
    {
        // dd(9);
        abort_unless(auth()->user()->hasPermissionTo('view_foodics_clients'), 403, 'You do not have permission to view this page.');
        return view('admin.pages.foodics-clients');
    }

    public function getClientsData(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('view_foodics_clients'), 403, 'You do not have permission to view this page.');

        $clients = Client::whereHas('client', function ($query) {
            $query->whereHas('integration', function ($q) {
                $q->where('id', 14);
            });
        });
        //    dd($clients->get());

        return DataTables::of($clients)
            ->addColumn('id', fn($row) => $row->id)
            ->addColumn('full_name', fn($row) => "{$row->first_name} {$row->last_name}")
            ->addColumn('integration_company', fn($row) => $row->client?->integration?->name)

            ->addColumn('action', function ($row) {
                if (!$row->foodics_token){
                    return "";
                }else{
                return '<div class="d-flex justify-content-center">
                <a href="#" data-id="' . $row->id . '"


                    class="flex items-center order-driver-btn justify-center w-8 h-8 text-white border rounded-lg border-gray10 callApiBtn">
                     <img src="/new/src/assets/icons/delete.svg" alt="Delete">
                </a>
            </div>';
                }
            })

            ->make(true);
    }

    public function revokeClientFoodicsToken(Request $request)
    {
        $client = Client::findOrFail($request->id);

        $url = 'https://api-sandbox.foodics.com/v5/tokens/client';
        $token = $client->foodics_token;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Accept: application/json',
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) {
            return response()->json([
                'message' => 'Token revoked successfully',
                'response' => json_decode($response, true)
            ], 200);
        } else {
            return response()->json([
                'message' => 'Failed to revoke token',
                'response' => json_decode($response, true),
                'http_code' => $httpCode
            ], $httpCode);
        }
    }
}
