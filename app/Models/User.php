<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Apartment;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'first_name',
        'last_name',
        'personal_photo',
        'id_photo',
        'birth_date',
        'phone',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'id_photo',
    ];

    // Simple admin check
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // User is automatically the opposite
    public function isUser()
    {
        return $this->role === 'user';
    }

    // For apartment owners, we'll check by ownership rather than role
    public function isApartmentOwner($apartmentId = null)
    {
        if ($apartmentId) {
            return $this->apartments()->where('id', $apartmentId)->exists();
        }
        return $this->apartments()->exists();
    }

    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'owner_id');
    }
    
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'user_id');
    }
    
    public function favorites()
    {
        return $this->belongsToMany(Apartment::class, 'favorites', 'user_id', 'apartment_id');
    }

    public function ratings()
    {
        return $this->belongsToMany(Apartment::class, 'ratings', 'user_id', 'apartment_id')
            ->withPivot('rating', 'comment', 'created_at');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'birth_date' => 'date',
            'is_approved' => 'boolean',
            'password' => 'hashed',
        ];
    }
}