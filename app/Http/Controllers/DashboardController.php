<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Functions\UsefulFunctions;
use App\Models\EarningToWalletHistory;
use App\Models\EasySavings;
use App\Models\ExchangeRate;
use App\Models\SavingsDuration;
use App\Models\SavingsFrequency;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public UsefulFunctions $functions;

    public function __construct()
    {
        $this->functions = new UsefulFunctions();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        return Inertia::render('Client/Dashboard', [
            'dashboard_stats' => [

                'transactions' => $user->transactions()->count(),
                'invoices' => $user->transactions()->whereNotNull('invoice_number')->count(),
                'rates' => ExchangeRate::count(),
                'wallets' => $user->wallet_balance,
            ],
            'user' => $user,
            'business' =>  $user->businessRegistrations()->first(),
            'recent_transactions' => $user->transactions()->with('toCurrency', 'fromCurrency')->latest()->take(10)->get(),
            'recent_invoices' => $user->transactions()->with('toCurrency', 'fromCurrency')->whereNotNull('invoice_number')->latest()->take(10)->get(),
        ]);
    }


    public function showReferralLink(Request $request)
    {
        $user = Auth::user();
        $props['user'] = $user;
        return Inertia::render('ReferralLink', $props);
    }

    public function aboutUs(Request $request)
    {
        $user = Auth::user();
        $props['user'] = $user;
        return Inertia::render('AboutUs', $props);
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
