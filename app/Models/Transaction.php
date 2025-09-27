<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'trans_id',
        'user_id',
        'business_id',
        'from_currency_id',
        'to_currency_id',
        'amount_to_receive',
        'amount_to_pay',
        'status',
        'first_rate',
    ];


    public function scopeFilterStatus($query, $status)
    {
        return $query->when($status, function ($query, $status) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        });
    }

    public function scopeFilterBusinessName($query, $business_name)
    {
        $query->whereHas('business', function ($q) use ($business_name) {
            $q->where('business_name', 'like', "%$business_name%");
        });
    }

    public function scopeFilterUserName($query, $name)
    {
        $query->whereHas('user', function ($q) use ($name) {
            $q->where('name', 'like', "%$name%");
        });
    }



    public function scopeFilterEmail($query, $email)
    {
        $query->whereHas('user', function ($q) use ($email) {
            $q->where('email', 'like', "%$email%");
        });
    }

    public function scopeFilterFullName($query, $name)
    {
        $query->whereHas('user', function ($q) use ($name) {
            $q->where('name', 'like', "%$name%");
        });
    }

    public function scopeFilterTransId($query, $trans_id)
    {
        $query->when($trans_id, function ($q) use ($trans_id) {
            $q->where('trans_id', 'like', "%$trans_id%");
        });
    }

    public function scopeFilterReceiptNumber($query, $receipt_number)
    {
        $query->when($receipt_number, function ($q) use ($receipt_number) {
            $q->where('receipt_number', 'like', "%$receipt_number%");
        });
    }

    public function scopeFilterInvoiceNumber($query, $invoice_number)
    {
        $query->when($invoice_number, function ($q) use ($invoice_number) {
            $q->where('invoice_number', 'like', "%$invoice_number%");
        });
    }



    public function scopeFilterAmountToReceive($query, $value)
    {
        return $query->when($value, fn($q) => $q->where('amount_to_receive', 'like', "%$value%"));
    }

    public function scopeFilterAmountToPay($query, $value)
    {
        return $query->when($value, fn($q) => $q->where('amount_to_pay', 'like', "%$value%"));
    }

    public function scopeFilterFinalAmountToPay($query, $value)
    {
        return $query->when($value, fn($q) => $q->where('final_amount_to_pay', 'like', "%$value%"));
    }


    public function scopeFilterFromCurrency($query, $currency)
    {
        return $query->when($currency, function ($q) use ($currency) {
            if (is_numeric($currency)) {
                $q->where('from_currency_id', (int)$currency);
            } else {
                $q->whereHas('fromCurrency', function ($qc) use ($currency) {
                    $qc->where('code', 'like', "%{$currency}%")
                        ->orWhere('name', 'like', "%{$currency}%");
                });
            }
        });
    }

    /**
     * Filter by "to" currency: same behavior as above.
     */
    public function scopeFilterToCurrency($query, $currency)
    {
        return $query->when($currency, function ($q) use ($currency) {
            if (is_numeric($currency)) {
                $q->where('to_currency_id', (int)$currency);
            } else {
                $q->whereHas('toCurrency', function ($qc) use ($currency) {
                    $qc->where('code', 'like', "%{$currency}%")
                        ->orWhere('name', 'like', "%{$currency}%");
                });
            }
        });
    }

    public function scopeFilterDate($query, $date)
    {
        if (!is_null($date)) {
            return $query->whereDate('created_at', date('Y-m-d', strtotime($date)));
        }
    }


    public function scopeFilterBetweenDates($query, $start_date, $end_date)
    {
        if (!is_null($start_date) && !is_null($end_date)) {
            return $query->whereBetween('created_at', [$start_date, $end_date]);
        }
    }

    public function scopeFilterInvoiceGeneratedDate($query, $date)
    {
        if (!is_null($date)) {
            return $query->whereDate('invoice_generated_at', date('Y-m-d', strtotime($date)));
        }
    }

    public function scopeFilterBetweenInvoiceGeneratedDates($query, $start_date, $end_date)
    {
        if (!is_null($start_date) && !is_null($end_date)) {
            return $query->whereBetween('invoice_generated_at', [$start_date, $end_date]);
        }
    }

    public function scopeFilterReceiptGeneratedDate($query, $date)
    {
        if (!is_null($date)) {
            return $query->whereDate('receipt_generated_at', date('Y-m-d', strtotime($date)));
        }
    }

    public function scopeFilterBetweenReceiptGeneratedDates($query, $start_date, $end_date)
    {
        if (!is_null($start_date) && !is_null($end_date)) {
            return $query->whereBetween('receipt_generated_at', [$start_date, $end_date]);
        }
    }

    public function business()
    {
        return $this->belongsTo(BusinessRegistration::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function fromCurrency()
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }
    public function toCurrency()
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }
}
