<?php

namespace App\Models;

use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends  User  implements HasMedia
{
    use HasFactory;
    use HasRoles, InteractsWithMedia;
    protected string $guard_name = 'web';
    const  ROLE = 'client';
    protected $table = 'users';

    protected $casts = [
        'billing_emails' => 'array',
        'auto_generate_invoice' => 'boolean',
        'last_invoice_date' => 'date',
    ];

    public function getMorphClass(): string {
        return User::class;
    }

    protected static function booted() {
        parent::booted();
        static::creating(fn($model) => $model->assignRole('client'));
        static::addGlobalScope("client", function ($builder) {
           //$builder->whereHas("roles", fn($q) => $q->where('name', 'client'));
            $builder->where('user_role',2);
        });
    }

    public function client () {

        return $this->hasOne(ClientDetail::class, 'user_id');
    }

    public function users () {
        return $this->hasMany(ClientUsers::class);
    }

    public function orders () {
        return $this->hasMany(Order::class, 'ingr_shop_id');
    }

    public function branches () {
        return $this->hasMany(ClientBranches::class, 'client_id');
    }

    public function wallet(){
        return $this->hasOne(Wallet::class, 'operator_id');
    }
    public function getImageUrlAttribute()
    {
        $pathdafult =  asset('new/src/assets/images/logo-2.png');
        if (isset($this->attributes['image'])) {
            if ($this->attributes['image'] != null) {
                $path = 'https://alshrouqdelivery.b-cdn.net/'.$this->attributes['image'];
                if (file_exists( $path )) {
                    return $pathdafult;
                } else {
                    return $path;
                }
            }
        }
        return $pathdafult;
    }


    public function calculateAvgTime($startColumn, $endColumn)
    {
        $durations = $this->orders->filter(function ($order) use ($startColumn, $endColumn) {
            return $order->$startColumn && $order->$endColumn;
        })->map(function ($order) use ($startColumn, $endColumn) {
            $startDate = Carbon::parse($order->$startColumn);
            $endDate = Carbon::parse($order->$endColumn);



            $diffInSeconds = $startDate->diffInSeconds($endDate);

            return $diffInSeconds;
        });

        if ($durations->isEmpty()) {
            return '00:00:00';
        }

        $avgSeconds = $durations->avg();

        $hours = floor($avgSeconds / 3600);
        $minutes = floor(($avgSeconds % 3600) / 60);
        $seconds = $avgSeconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

}
