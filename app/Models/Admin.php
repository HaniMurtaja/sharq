<?php

namespace App\Models;

use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends  User  implements HasMedia
{
    use HasFactory;
    use HasRoles, InteractsWithMedia;
    protected string $guard_name = 'web';
    const  ROLE = 'admin';
    protected $table = 'users';

    public function getMorphClass(): string {
        return User::class;
    }

    protected static function booted() {
        parent::booted();
        static::creating(fn($model) => $model->assignRole('admin'));
        static::addGlobalScope("admin", function ($builder) {
            $builder->whereHas("roles", fn($q) => $q->where('name', 'admin'));
        });
    }

    
}
