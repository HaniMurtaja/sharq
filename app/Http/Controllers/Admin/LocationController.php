<?php

namespace App\Http\Controllers\Admin;

use App\Enum\UserRole;
use Exception;


use App\Models\Area;

use App\Models\City;
use App\Models\Country;

use Illuminate\Http\Request;

use App\Http\Requests\AreaRequest;

use App\Http\Requests\CityRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\CountryRequest;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;


class LocationController  extends Controller
{



    public function countryList(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');

        $columns = ['id', 'name'];
        $user = auth()->user();
        $countryId = $user->country_id;

        $query = Country::query();


        if ($countryId) {
            $query->where('id', $countryId);
        }

        $totalData = $query->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');

        $order = $columns[$orderColumn] ?? $columns[0];

        if (empty($request->input('search.value'))) {
            $countries = $query->offset($start)
                ->limit($limit)
                ->orderBy($order, $orderDir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $query->where('name', 'LIKE', "%{$search}%");

            $totalFiltered = $query->count();

            $countries = $query->offset($start)
                ->limit($limit)
                ->orderBy($order, $orderDir)
                ->get();
        }

        $data = [];
        foreach ($countries as $country) {
            $data[] = [
                'id' => $country->id,
                'name' => $country->name,
            ];
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data,
        ]);
    }


    public function editCountry($id = null)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');

        $country = Country::findOrFail($id);
        return response()->json(['country' => $country]);
    }

    public function updateCountry(CountryRequest $request, $id)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');

        $country = Country::findOrFail($id);
        $country->update([
            'name' => $request['country_name'],

        ]);
    }


    public function saveCountry(CountryRequest $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');


        try {

            if ($request->country_id) {
                $this->updateCountry($request, $request->country_id);
            } else {
                Country::create([
                    'name' => $request->country_name,
                ]);
            }

            return response()->json(['message' => 'Country saved successfully']);
        } catch (Exception $e) {

            return response()->json(['error' => 'Failed to save country'], 500);
        }
    }

    public function deleteCountry($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');

        $country = Country::findOrFail($id);
        $country->delete();
        return response()->json('country deleted successfully');
    }





    public function cityList(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');

        $columns = ['id', 'name'];
        $user = auth()->user();
        $countryId = $user->country_id;

        $query = City::query();


        if ($countryId) {
            $query->where('country_id', $countryId);
        }

        $totalData = $query->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');

        $order = $columns[$orderColumn] ?? $columns[0];

        if (empty($request->input('search.value'))) {
            $cities = $query->offset($start)
                ->limit($limit)
                ->orderBy($order, $orderDir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $query->where('name', 'LIKE', "%{$search}%");

            $totalFiltered = $query->count();

            $cities = $query->offset($start)
                ->limit($limit)
                ->orderBy($order, $orderDir)
                ->get();
        }

        $data = [];
        foreach ($cities as $city) {
            $data[] = [
                'id' => $city->id,
                'name' => $city->name,
            ];
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data,
        ]);
    }



    public function editCity($id = null)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');

        $city = City::findOrFail($id);

        return response()->json(['city' => $city]);
    }

    public function updateCity(CityRequest $request, $id)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');

        $city = City::findOrFail($id);
        $city->update([
            'name' => $request['city_name'],
            'country_id' => $request['country_id'],
            'lat' => $request->lat,
            'lng' => $request->lng,
            'auto_dispatch' => $request->auto_dispatch ?? 0,

        ]);
    }


    public function saveCity(CityRequest $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');
        try {

            if ($request->city_id) {
                $this->updateCity($request, $request->city_id);
            } else {
                City::create([
                    'name' => $request->city_name,
                    'country_id' => $request->country_id,
                    'lat' => $request->lat,
                    'lng' => $request->lng,
                    'auto_dispatch' => $request->auto_dispatch ?? 0,
                ]);
            }





            return response()->json(['message' => 'City saved successfully']);
        } catch (Exception $e) {

            return response()->json(['error' => 'Failed to save city'], 500);
        }
    }

    public function deleteCity($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');

        $city = City::findOrFail($id);
        $city->delete();
        return response()->json('city deleted successfully');
    }


    public function areaList(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');

        $columns = ['id', 'name'];

        $user = auth()->user();
        $countryId = $user->country_id;

        $query = Area::query();

        
        if ($countryId) {
            $query->whereHas('city', function ($q) use ($countryId) {
                $q->where('country_id', $countryId);
            });
        }

        $totalData = $query->count();
        $totalFiltered = $totalData;

        $limit = $request->input('length', 10);
        $start = $request->input('start', 0);
        $orderColumn = $request->input('order.0.column', 0);
        $orderDir = $request->input('order.0.dir', 'asc');

        $order = $columns[$orderColumn] ?? $columns[0];

        if (empty($request->input('search.value'))) {
            $areas = $query->offset($start)
                ->limit($limit)
                ->orderBy($order, $orderDir)
                ->get();
        } else {
            $search = $request->input('search.value');

            $query->where('name', 'LIKE', "%{$search}%");
            $totalFiltered = $query->count();

            $areas = $query->offset($start)
                ->limit($limit)
                ->orderBy($order, $orderDir)
                ->get();
        }

        $data = [];
        foreach ($areas as $area) {
            $data[] = [
                'id' => $area->id,
                'name' => $area->name,
            ];
        }

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $totalData,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        ]);
    }

    public function editArea($id = null)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');
        $area = Area::findOrFail($id);
        return response()->json(['area' => $area]);
    }

    public function updateArea(AreaRequest $request, $id)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');

        $area = Area::findOrFail($id);
        $area->update([
            'name' => $request['area_name'],
            'city_id' => $request['city_id']
        ]);
    }


    public function saveArea(AreaRequest $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');


        try {

            if ($request->area_id) {
                $this->updateArea($request, $request->area_id);
            } else {
                Area::create([
                    'name' => $request['area_name'],
                    'city_id' => $request['city_id']
                ]);
            }

            return response()->json(['message' => 'Area saved successfully']);
        } catch (Exception $e) {

            return response()->json(['error' => 'Failed to save area'], 500);
        }
    }

    public function deleteArea($id)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');

        $area = Area::findOrFail($id);
        $area->delete();
        return response()->json('area deleted successfully');
    }


    public function cityAreas(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');


        $areas = Area::where('city_id', $request->city_id)->get();

        return response()->json($areas);
    }






    public function resolveUrl(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('controle_location'), 403, 'You do not have permission to view this page.');

        $shortUrl = $request->input('url');

        if (!$shortUrl) {
            return response()->json(['error' => 'No URL provided'], 400);
        }

        // Guzzle client with redirect tracking middleware
        $historyContainer = [];
        $stack = HandlerStack::create();
        $stack->push(Middleware::history($historyContainer));

        $client = new Client(['handler' => $stack]);

        try {
            $response = $client->get($shortUrl, ['http_errors' => false]);

            // Check if there were any redirects
            if (count($historyContainer) > 0) {
                // Get the last response in the history (this is the final destination URL)
                $lastTransaction = end($historyContainer);
                $resolvedUrl = (string) $lastTransaction['response']->getHeaderLine('Location') ?: (string) $lastTransaction['request']->getUri();
            } else {
                // No redirects, use the original URL
                $resolvedUrl = (string) $response->getHeaderLine('Location') ?: $shortUrl;
            }

            // Extract lat/lng from the resolved URL
            $coordinates = $this->extractLatLng($resolvedUrl);

            if ($coordinates) {
                return response()->json($coordinates);
            } else {
                return response()->json(['error' => 'Could not extract lat/lng'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Invalid URL format'], 400);
        }
    }



    private function extractLatLng($url)
    {
        // Regex to find lat/lng in Google Maps URL
        $pattern = '/@([-+]?[\d.]+),([-+]?[\d.]+)/';
        preg_match($pattern, $url, $matches);

        if (count($matches) >= 3) {
            return [
                'lat' => $matches[1],
                'lng' => $matches[2],
            ];
        }

        return null;
    }
}
