<?php

namespace App\Models;

use App\Enum\FeedType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;

    protected $fillable = ['name',	'min_feed_order',	'type_feed_order',	'additional_feed_order'	];

    protected $casts = [
        'type_feed_order' => FeedType::class ,
        'additional_feed_order' => FeedType::class
    ];
    public function conditions () {
        return $this->hasMany(GroupCondition::class);
    }
}
