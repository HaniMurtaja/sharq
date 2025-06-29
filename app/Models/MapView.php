<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MapView extends Model
{
    use HasFactory;
    protected $table = 'map_view';  // Link model to the view
    public $timestamps = false;      // Views usually don't have timestamps
}
