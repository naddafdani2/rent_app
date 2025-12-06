<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    protected $fillable = [
        'title',
        'description',
        'total_area',
        'price_per_day',
        'price_per_month',
        'state',
        'city',
        'street',
        'building_number',
        'level',
        'is_available',
        'owner_id',
    ];


    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function images()
    {
        return $this->hasMany(Apt_image::class, 'apartment_id');
    }
}