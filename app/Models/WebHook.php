<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebHook extends Model
{
    use HasFactory;
    protected $guarded = [];



    public function company () {
        return $this->belongsTo(IntegrationCompany::class, 'integration_company_id');
    }
}
