<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IntegrationCompany extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'has_cancel_reason',
        'client_type',
        'otp_awb',
    ];
    public function webhooks()
    {
        return $this->hasMany(WebHook::class, 'integration_company_id', 'id');
    }
}
