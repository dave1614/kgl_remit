<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FxController extends Controller
{
    public function index()
    {


        $currencies = Currency::orderBy('code')->get();
        $exchangeRates = ExchangeRate::select('from_currency_id', 'to_currency_id', 'rate')->get();

        return Inertia::render('ExchangeRates/FxCalculator', [
            'currencies' => $currencies,
            'exchangeRates' => $exchangeRates
        ]);
    }

    public function calculate(Request $request)
    {
        $data = $request->validate([
            'from_currency_id' => 'required|exists:currencies,id',
            'to_currency_id'   => 'required|exists:currencies,id',
            'amount'           => 'required|numeric|min:0.01'
        ]);

        $rate = ExchangeRate::with(['fromCurrency', 'toCurrency'])
            ->where('from_currency_id', $data['from_currency_id'])
            ->where('to_currency_id', $data['to_currency_id'])
            ->firstOrFail();

        $converted = bcmul($data['amount'], $rate->rate, 6);

        return response()->json([
            'rate' => $rate->rate,
            'converted_amount' => $converted,
            'from_currency' => $rate->fromCurrency,
            'to_currency' => $rate->toCurrency
        ]);
    }
}
