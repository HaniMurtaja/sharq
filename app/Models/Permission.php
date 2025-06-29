<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Casts\EnumCast;
use App\Enum\Permissions as PermissionEnum;
use Spatie\Permission\Models\Permission as SpatiePermission;
class Permission extends SpatiePermission
{
    use HasFactory;
    protected $table="permissions";
   
    protected $casts = [
        'name' => EnumCast::class . ':' . PermissionEnum::class,
    ];
}
