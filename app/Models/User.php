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

    public function scopeFilterStatus($query, $status)
    {
        return $query->when($status, function ($query, $status) {
            if($status == 'all'){

            }else if($status == "verified"){
                $query->whereNotNull('email_verified_at');
            }else if($status == "unverified"){
                $query->whereNull('email_verified_at');
            }else if($status == "business_registered"){
                $query->where('business_registered', 1);
            }else if($status == "business_registration_pending"){
                $query->where('business_registered', 0);
            }


        });
    }

    public function scopeFilterUserName($query, $user_name)
    {
        $query->when($user_name, function ($query, $user_name) {
            $query->where("user_name", "like", "%$user_name%");
        });
    }

    public function scopeFilterFullName($query, $full_name)
    {
        $query->when($full_name, function ($query, $full_name) {
            $query->where("name", "like", "%$full_name%");
        });
    }

    public function scopeFilterPhone($query, $phone)
    {
        $query->when($phone, function ($query, $phone) {
            $query->where("phone", "like", "%$phone%");
        });
    }

    public function scopeFilterEmail($query, $email)
    {
        $query->when($email, function ($query, $email) {
            $query->where("email", "like", "%$email%");
        });
    }

    public function scopeFilterDate($query, $date)
    {
        if (!is_null($date)) {
            return $query->whereDate('created_at', date('Y-m-d', strtotime($date)));
        }
    }

    public function scopeFilterEmailVerifiedDate($query, $date)
    {
        if (!is_null($date)) {
            return $query->whereDate('email_verified_at', date('Y-m-d', strtotime($date)));
        }
    }

    public function scopeFilterBusinessRegisteredDate($query, $date)
    {
        if (!is_null($date)) {
            return $query->whereDate('business_registered_at', date('Y-m-d', strtotime($date)));
        }
    }

    public function scopeFilterBetweenDates($query, $start_date, $end_date)
    {
        if (!is_null($start_date) && !is_null($end_date)) {
            return $query->whereBetween('created_at', [$start_date, $end_date]);
        }
    }

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

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
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
