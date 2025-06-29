<?php

namespace App\Livewire;

use Livewire\Component;
use App\Http\Services\GoogleLocationEncoder;

class MapSearchBox extends Component
{
    public $address;

    public function search()
    {

        $coordinates = new GoogleLocationEncoder(
            $this->address
        );
        // dd($coordinates->getLatitude());

        $this->dispatch('updatedMapLocation', [
            'lat' => $coordinates->getLatitude(),
            'lng' => $coordinates->getLongitude()
        ]);
    }
    

    public function render()
    {
        return view('livewire.map-search-box');
    }
}
