<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupCondition extends Model
{
    use HasFactory;
    protected $fillable = ['group_id'	,'feed_type'	,'data'];
    protected $casts = [
        'data' => 'json',
    ];
    public function group () {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
