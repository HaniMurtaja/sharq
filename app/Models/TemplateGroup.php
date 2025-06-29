<?php

namespace App\Models;

use App\Enum\PermissionGroups;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateGroup extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $casts = [
        'name' => PermissionGroups::class
    ];



}
