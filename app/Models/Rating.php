<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;
    protected $fillable = [
        'rate',
        'comment',
        'user_id',
        'apartment_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function apartment()
    {
        return $this->belongsTo(Apartment::class);
    }
}