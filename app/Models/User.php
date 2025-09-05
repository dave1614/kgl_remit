<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Functions\UsefulFunctions;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\FollowUserNotification;
use App\Notifications\UnFollowUserNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;



    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [

        'name',
        'user_name',
        'slug',
        'email',
        'country_id',
        'is_admin',
        'phone',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    protected $appends = ['country_details'];

    // public function getRouteKeyName(): string
    // {
    //     return 'slug';
    // }



    public function countryDetails(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->country()->first()
        );
    }

    public function businessRegistrations(): HasMany
    {
        return $this->hasMany(BusinessRegistration::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    protected function phoneCode(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => Country::find($attributes['country_id'])->phone_code,
        );
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new CustomVerifyEmail);
    }
}
