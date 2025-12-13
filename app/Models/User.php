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
    use HasFactory, Notifiable,HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'personal_photo',
        'id_photo',
        'birth_date',
        'phone',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'id_photo',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    
    public function apartments()
    {
        return $this->hasMany(Apartment::class, 'owner_id');
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