<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'start_date',
        'end_date',
        'user_id',
        'apartments_id',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }
    public function apartment()
    {
        return $this->belongsTo(Apartment::class,'apartments_id');
    }
}
