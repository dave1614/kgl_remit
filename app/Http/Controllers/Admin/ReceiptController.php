<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReceiptController extends Controller
{
    public function index(Request $request)
    {

        $trans_id = $request->query('trans_id');
        $receipt_number = $request->query('receipt_number');
        $status = $request->query('status');

        $props['trans_id'] = $trans_id;
        $props['receipt_number'] = $receipt_number;
        $props['status'] = $status;
        // Return Inertia page
        return Inertia::render('Admin/Transactions/Receipts', $props);
    }

    public function viewAllReceipts(Request $request)
    {
        $length = $request->query('length', 10);

        $transactions = Transaction::with('user', 'business', 'fromCurrency', 'toCurrency')
            ->addSelect('transactions.*')
            // only transactions that have invoice numbers
            ->whereNotNull('receipt_number')
            ->filterStatus($request->query('status'))
            ->filterReceiptNumber($request->query('receipt_number'))
            ->filterBusinessName($request->query('business_name'))
            ->filterUserName($request->query('user_name'))
            ->filterEmail($request->query('email'))
            ->filterFullName($request->query('full_name'))
            ->filterFinalAmountToPay($request->query('amount'))
            ->filterTransId($request->query('trans_id'))
            ->filterReceiptGeneratedDate($request->query('date'))
            ->filterBetweenReceiptGeneratedDates($request->query('start_date'), $request->query('end_date'))
            ->orderBy('id', 'DESC')
            ->paginate($length)
            ->withQueryString();

        return $transactions;
    }

}
