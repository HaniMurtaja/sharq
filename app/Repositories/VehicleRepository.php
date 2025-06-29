<?php
    namespace App\Repositories;

    use App\Http\Resources\Api\VehicleResource;
    use App\Models\Vehicle;
    use App\Traits\HandleResponse;
    use Illuminate\Http\Request;
    use Validator;

    class VehicleRepository
    {
        use HandleResponse;

        public function __construct()
        {

        }

        public function add_vehicle(Request $request)
        {
            $validator = Validator::make($request->all(), [
                'vehicle_image' => ['nullable', 'image'],
                'id_card_image' => ['nullable', 'image'],
                'name' => ['required', 'string'],
                'type' => ['required', 'string'],
                'plate_number' => ['required', 'string'],
                'vin_number' => ['nullable', 'string'],
                'make' => ['nullable', 'string'],
                'model' => ['nullable', 'string'],
                'year' => ['required',  'digits:4'],
                'color' => ['required', 'string'],
                'vehicle_milage' => ['nullable', 'numeric'],
                'last_service_milage' => ['nullable', 'numeric'],
                'due_service_milage' => ['nullable', 'numeric'],
                'service_milage_limit' => ['nullable', 'numeric'],
            ]);
            if($validator->fails())
            {
                return $this->send_response(FALSE, 400, $validator->errors()->first(), NULL);
            }
            //find vehicle exist or create new
            $prev = Vehicle::where('operator_id', auth()->user()->id)->first();
            if($prev)
            {
                $vehicle = $prev;
            }
            else
            {
                $vehicle = new Vehicle();
            }
            // Assign each validated field to the Vehicle model
            $vehicle->operator_id = auth()->user()->id;
            $vehicle->name = $request->name;
            $vehicle->type = $request->type;
            $vehicle->plate_number = $request->plate_number;
            $vehicle->vin_number = $request->vin_number;
            $vehicle->make = $request->make;
            $vehicle->model = $request->model;
            $vehicle->year = $request->year;
            $vehicle->color = $request->color;
            $vehicle->vehicle_milage = $request->vehicle_milage;
            $vehicle->last_service_milage = $request->last_service_milage;
            $vehicle->due_service_milage = $request->due_service_milage;
            $vehicle->service_milage_limit = $request->service_milage_limit;
            // Save the Vehicle instance to the database
            $vehicle->save();
            if($request['vehicle_image'])
            {
                $vehicle->clearMediaCollection('vehicle_image');
                $vehicle->addMedia($request['vehicle_image'])->toMediaCollection('vehicle_image');
            }
            if($request['id_card_image'])
            {
                $vehicle->clearMediaCollection('id_card_image');
                $vehicle->addMedia($request['id_card_image'])->toMediaCollection('id_card_image');
            }
            return $this->send_response(TRUE, 200, 'success', new VehicleResource($vehicle));
        }
    }
