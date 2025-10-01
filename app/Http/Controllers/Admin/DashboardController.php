<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessRegistration;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Stats
        $stats = [
            'users' => User::count(),
            'businesses' => BusinessRegistration::count(),
            'transactions' => Transaction::count(),
            'pending_transactions' => Transaction::where('status', 'pending_request')->count(),
            'invoices' => Transaction::whereNotNull('invoice_number')->count(),
        ];

        // Recent items
        $recentUsers = User::latest()->take(5)->get();
        $recentTransactions = Transaction::with('user', 'fromCurrency', 'toCurrency')
            ->latest()->take(5)->get();
        $recentInvoices = Transaction::whereNotNull('invoice_number')->with('user')->latest()->take(5)->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recent_users' => $recentUsers,
            'recent_transactions' => $recentTransactions,
            'recent_invoices' => $recentInvoices,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
