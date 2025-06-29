<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BankDetails extends Model
{
    use HasFactory;
    protected $table = "driver_bank_details";
    protected $guarded = [];

    public function operator() : BelongsTo{
        return $this->belongsTo(Operator::class, 'operator_id');
    }
}
