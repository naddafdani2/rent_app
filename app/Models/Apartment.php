<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    use HasFactory;

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

    protected static function booted()
    {
        static::deleting(function ($apartment) {
            $apartment->images()->delete();
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function images()
    {
        return $this->hasMany(Apt_image::class, 'apartment_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'apartment_id');
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites', 'apartment_id', 'user_id');
    }

    public function ratingBy()
    {
        return $this->belongsToMany(User::class, 'ratings', 'apartment_id', 'user_id')->withPivot('rate', 'comment');
    }
}