<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExportLog extends Model
{
    protected $fillable = [
    'user_id',
    'file_name',
    'file_path',
    'is_ready',
    'country_id'
];


    public function getFullPatchAttribute()
    {

        $file = @$this->attributes['file_path'];


        return $file ? env('IMAGE_URL') . $file : null;
    }
}
