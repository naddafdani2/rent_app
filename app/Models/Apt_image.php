<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Apt_image extends Model
{
    protected $fillable = [
        'image_path',
        'is_primary',
        'apartment_id',
    ];

    public function apartment()
    {
        return $this->belongsTo(Apartment::class, 'apartment_id');
    }
}