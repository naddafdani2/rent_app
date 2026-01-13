<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Model;

class Apt_image extends Model
{
    use HasFactory;
       
    protected $fillable = [
        'image_path',
        'is_primary',
        'apartment_id',
    ];
protected static function booted()
    {
       static::deleting(function ($image) {
        
        $path = $image->image_path;

        if (Storage::disk('public')->exists($path)) {
            $result = Storage::disk('public')->delete($path);
       
        }
    });
    }
    public function apartment()
    {
        return $this->belongsTo(Apartment::class, 'apartment_id');
    }
}