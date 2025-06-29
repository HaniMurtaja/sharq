<?php
namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class VehiclesExport implements FromCollection, WithHeadings
{
    protected $vehicles;

    public function __construct($vehicles)
    {
        $this->vehicles = $vehicles;
    }

    public function collection()
    {
        // Transform the operators into the desired format
        return $this->vehicles->map(function ($vehicle) {
            return [
                'id' => $vehicle->id,
                'name' => $vehicle->name,
                'type' => $vehicle->type,
               
                'plate_number' => $vehicle->plate_number,
                'vin_number' => $vehicle->vin_number,
                'make' => $vehicle->make,

                'model' => $vehicle->model,
                'year' => $vehicle->year,
                'color' => $vehicle->color,

                'created_at' => $vehicle->created_at,
             
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Type',
            'Plate number',
            'Vin number',
            'Make',
            'Model',
            'Year',
            'color',
            'Creation Date',
        ];
    }
}
