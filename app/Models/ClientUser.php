<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;

use Spatie\MediaLibrary\InteractsWithMedia;
class ClientUser extends User  implements HasMedia
{
    use HasFactory;
    use HasRoles, InteractsWithMedia;
    protected string $guard_name = 'web';
    const  ROLE = 'user';
    protected $table = 'users';

    public function getMorphClass(): string {
        return User::class;
    }

    // protected static function booted() {
    //     parent::booted();
    //     static::creating(fn($model) => $model->assignRole('user'));
    //     static::addGlobalScope("user", function ($builder) {
    //         $builder->whereHas("roles", fn($q) => $q->where('name', 'user'));
    //     });
    // }

    public function user () {

        return $this->hasOne(UserDetail::class, 'user_id');
    }


}
