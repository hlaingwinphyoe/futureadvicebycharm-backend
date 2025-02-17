<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'nickname',
        'phone',
        'email',
        'dob',
        'social_link',
        'day_id',
        'gender_id',
        'weekday_id',
        'role_id',
        'email_verified_at',
        'profile_photo_path',
        'disabled',
        'password',
        'provider',
        'provider_id',
        'provider_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function gender()
    {
        return $this->belongsTo(Status::class, 'gender_id');
    }

    public function weekday()
    {
        return $this->belongsTo(Status::class, 'weekday_id');
    }

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }

    protected function profile(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                return $attributes['profile_photo_path'] !== null ? '/storage/' . $attributes['profile_photo_path'] : '';
            }
        );
    }

    // scope function
    public function scopeNotAdmin($q)
    {
        $q->whereHas("role", function ($query) {
            $query->whereNotIn('slug', ['admin', 'developer']);
        });
    }

    public function scopeCustomer($q)
    {
        $q->whereHas("role", function ($query) {
            $query->where('slug', 'user');
        });
    }

    public function scopeNotCustomer($q)
    {
        $q->whereHas("role", function ($query) {
            $query->where('slug', '!=', 'user');
        });
    }

    public function scopeAstrologer($q)
    {
        $q->whereHas("role", function ($query) {
            $query->where('slug', 'astrologer');
        });
    }

    public function scopeFilterOn($query)
    {
        if (request('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }

        if (request('role')) {
            $query->where('role_id', request('role'));
        }
    }
}
