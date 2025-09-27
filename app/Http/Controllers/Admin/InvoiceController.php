<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $trans_id = $request->query('trans_id');
        $invoice_number = $request->query('invoice_number');
        $status = $request->query('status');

        $props['trans_id'] = $trans_id;
        $props['invoice_number'] = $invoice_number;
        $props['status'] = $status;
        // Return Inertia page
        return Inertia::render('Admin/Transactions/Invoices', $props);
    }

    public function viewAllInvoices(Request $request)
    {
        $length = $request->query('length', 10);

        $transactions = Transaction::with('user', 'business', 'fromCurrency', 'toCurrency')
            ->addSelect('transactions.*')
            // only transactions that have invoice numbers
            ->whereNotNull('invoice_number')
            ->filterStatus($request->query('status'))
            ->filterInvoiceNumber($request->query('invoice_number'))
            ->filterBusinessName($request->query('business_name'))
            ->filterUserName($request->query('user_name'))
            ->filterEmail($request->query('email'))
            ->filterFullName($request->query('full_name'))
            ->filterFinalAmountToPay($request->query('amount'))
            ->filterTransId($request->query('trans_id'))
            ->filterInvoiceGeneratedDate($request->query('date'))
            ->filterBetweenInvoiceGeneratedDates($request->query('start_date'), $request->query('end_date'))
            ->orderBy('id', 'DESC')
            ->paginate($length)
            ->withQueryString();

        return $transactions;
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
