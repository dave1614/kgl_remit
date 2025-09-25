<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class BusinessRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'country_id',
        'status',
    ];

    protected $appends = ['certificate_url', 'moa_url', 'cosc_url', 'valid_id_url'];

    public function getCertificateUrlAttribute()
    {
        return $this->certificate ? Storage::url($this->certificate) : null;
    }

    public function getMoaUrlAttribute()
    {
        return $this->moa ? Storage::url($this->moa) : null;
    }

    public function getCoscUrlAttribute()
    {
        return $this->cosc ? Storage::url($this->cosc) : null;
    }

    public function getValidIdUrlAttribute()
    {
        return $this->valid_id ? Storage::url($this->valid_id) : null;
    }



    public function scopeFilterStatus($query, $status)
    {
        return $query->when($status, function ($query, $status) {
            if($status != 'all'){
                $query->where('status', $status);
            }

        });
    }


    public function scopeFilterBusinessName($query, $business_name)
    {
        $query->when($business_name, function ($query, $business_name) {
            $query->where("business_name", "like", "%$business_name%");
        });
    }

    public function scopeFilterPostalCode($query, $postal_code)
    {
        $query->when($postal_code, function ($query, $postal_code) {
            $query->where("postal_code", "like", "%$postal_code%");
        });
    }



    public function scopeFilterEmail($query, $email)
    {
        return $query->whereHas('user', function ($q) use ($email) {
            $q->where("email", "like", "%$email%");
        });
    }

    public function scopeFilterPhone($query, $phone)
    {
        $query->whereHas('user', function ($q) use ($phone) {
            $q->where("phone", "like", "%$phone%");
        });
    }

    public function scopeFilterCity($query, $city)
    {
        $query->whereHas('city', function ($query) use ($city) {
            $query->where('name', 'like', "%$city%");
        });
    }

    public function scopeFilterState($query, $state)
    {
        $query->whereHas('state', function ($query) use ($state) {
            $query->where("name", "like", "%$state%");
        });
    }


    public function scopeFilterUserName($query, $user_name)
    {
        $query->whereHas('user', function ($q) use ($user_name) {
            $q->where("user_name", "like", "%$user_name%");
        });
    }

    public function scopeFilterFullName($query, $name)
    {
        $query->whereHas('user', function ($q) use ($name) {
            $q->where("name", "like", "%$name%");
        });
    }

    public function scopeFilterDate($query, $date)
    {
        if (!is_null($date)) {
            return $query->whereDate('updated_at', date('Y-m-d', strtotime($date)));
        }
    }

    public function scopeFilterBetweenDates($query, $start_date, $end_date)
    {
        if (!is_null($start_date) && !is_null($end_date)) {
            return $query->whereBetween('updated_at', [$start_date, $end_date]);
        }
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(InecState::class);
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(InecLga::class);
    }
}
