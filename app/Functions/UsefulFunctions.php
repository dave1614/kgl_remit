<?php

namespace App\Functions;

use App\Models\BusinessRegistration;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class UsefulFunctions
{

    public function findLiveConversionRate($from, $to)
    {
        $rate = 0.00;
        $response = $this->interRateCurl($from, $to);

        if ($this->isJson($response)) {
            $response = json_decode($response);

            if ($response->result == "success") {
                $rate = $response->conversion_rate;
            }
        }

        return $rate;
    }

    public function interRateCurl($from, $to)
    {
        $url = "https://v6.exchangerate-api.com/v6/bf498b8e64e59eb79d4021f0/pair/{$from}/{$to}";
        $headers = [

            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-cache',

        ];

        $response = Http::withOptions([
            'http_errors' => false,
            'verify' => false,
        ])->withHeaders($headers)->get($url);

        return $response;
    }

    public function generateNewReceiptNo()
    {

        $receipt_no = 'R-' . strtoupper(Str::random(6));

        $exists = Transaction::where('receipt_number', $receipt_no)->first();
        return $exists ? $this->generateNewReceiptNo() : $receipt_no;
    }

    public function generateNewIncoiceId()
    {
        $inv_id = 'INV-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));

        $exists = Transaction::where('invoice_number', $inv_id)->first();
        return $exists ? $this->generateNewIncoiceId() : $inv_id;
    }

    public function generateNewTransactionId()
    {
        $trans_id = 'TXN' . mt_rand(100000000000, 999999999999);

        $exists = Transaction::where('trans_id', $trans_id)->first();
        return $exists ? $this->generateNewTransactionId() : $trans_id;
    }

    public function getAdminUser()
    {
        return User::where('is_admin', 1)->first();
    }

    public function giveUsersInitBusinesses()
    {
        $users = User::all();

        foreach ($users as $user) {
            if ($user->is_admin == 0) {
                BusinessRegistration::create([
                    'user_id' => $user->id,
                    'country_id' => 151,
                    'status' => null
                ]);
            }
        }
    }

    public function getAdminId()
    {

        $admin = User::where('is_admin', 1)->first();
        if ($admin) {
            return $admin->id;
        }
    }

    public function generateUniqueSlugForUser($user_name)
    {
        $slug = Str::slug($user_name);
        $user = User::where("slug", $slug)->first();
        if ($user) {
            $slug = $slug . "-" . rand(1000, 9999);
        }
        return $slug;
    }

    public function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
