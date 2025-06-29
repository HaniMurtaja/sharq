<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViewSearchOrder extends Model
{

    protected $table = 'view_search_orders';


    public $incrementing = false;
    public $timestamps = false;
    protected $guarded = [];


    protected $primaryKey = 'id';


    public function save(array $options = [])
    {
        throw new \Exception('ViewSearchOrder model is read only!');
    }
    public function delete()
    {
        throw new \Exception('ViewSearchOrder model is read only!');
    }
}
