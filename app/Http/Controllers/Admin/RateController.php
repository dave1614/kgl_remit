<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExchangeRate;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class RateController extends Controller
{
    public function index(Request $request)
    {
        // we also send currencies for the dropdowns
        $currencies = Currency::orderBy('code')->get();

        return Inertia::render('Admin/ExchangeRates/Index', [
            'currencies' => $currencies
        ]);
    }

    public function getCurrentRates(Request $request)
    {
        $query = ExchangeRate::with(['fromCurrency', 'toCurrency']);

        if ($request->from_currency_id) {
            $query->where('from_currency_id', $request->from_currency_id);
        }

        if ($request->to_currency_id) {
            $query->where('to_currency_id', $request->to_currency_id);
        }

        if ($request->rate_min) {
            $query->where('rate', '>=', $request->rate_min);
        }

        if ($request->rate_max) {
            $query->where('rate', '<=', $request->rate_max);
        }

        $exchangeRates = $query->orderBy('updated_at', 'desc')
            ->paginate($request->length ?? 10)
            ->withQueryString();

        return response()->json($exchangeRates);
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_currency_id' => ['required', 'exists:currencies,id'],
            'to_currency_id'   => [
                'required',
                'exists:currencies,id',
                'different:from_currency_id',
                Rule::unique('exchange_rates')
                    ->where(
                        fn($q) => $q
                            ->where('from_currency_id', $request->input('from_currency_id'))
                            ->where('to_currency_id', $request->input('to_currency_id'))
                    ),
            ],
            'rate' => ['required', 'numeric', 'min:0'],
        ], [
            // optional custom message
            'to_currency_id.unique' => 'This currency pair already exists.',
        ]);

        $rate = ExchangeRate::create($request->only('from_currency_id', 'to_currency_id', 'rate'));

        return response()->json($rate->load(['fromCurrency', 'toCurrency']));
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'from_currency_id' => 'required|exists:currencies,id',
            'to_currency_id' => 'required|exists:currencies,id|different:from_currency_id',
            'rate' => 'required|numeric|min:0',
        ]);

        $rate = ExchangeRate::findOrFail($id);
        $rate->update($request->only('from_currency_id', 'to_currency_id', 'rate'));

        return response()->json($rate->load(['fromCurrency', 'toCurrency']));
    }

    public function destroy($id)
    {
        $rate = ExchangeRate::findOrFail($id);
        $rate->delete();

        return response()->json(['message' => 'Exchange rate deleted']);
    }
}
