<?php

namespace App\Http\Controllers\Admin;

use App\Functions\UsefulFunctions;
use App\Http\Controllers\Controller;
use App\Models\BusinessRegistration;
use App\Models\ExchangeRate;
use App\Models\Setting;
use App\Models\Transaction;
use App\Notifications\TransactionInvoiceNotification;
use App\Notifications\UserPaymentReceivedNotification;
use App\Notifications\UserTransactionProcessedNotification;
use App\Notifications\UserTransactionRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Illuminate\Support\Str;


class TransactionController extends Controller
{

    public UsefulFunctions $functions;

    public function __construct()
    {
        $this->functions = new UsefulFunctions();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $trans_id = $request->query('trans_id');
        $status = $request->query('status');

        $props['trans_id'] = $trans_id;
        $props['status'] = $status;
        // Return Inertia page
        return Inertia::render('Admin/Transactions/Index', $props);
    }


    public function reject(Request $request, Transaction $transaction)
    {
        $request->validate([
            'reason' => 'required|string|max:5000',
        ]);


        $transaction->status = 'rejected';
        $transaction->rejection_reason = $request->reason;
        $transaction->rejected_at = now();

        $transaction->save();

        // Notify the user
        $transaction->user->notify(new UserTransactionRejectedNotification($transaction, $request->reason));

        return response()->json([
            'success' => true,
            'message' => 'Transaction rejected successfully',
        ]);
    }

    public function markPaymentProcessed(Transaction $transaction)
    {
        // optionally validate that status is payment_received first

        $transaction->status = 'completed';
        $transaction->processed_at = now();

        $transaction->save();

        // trigger events/notifications

        $user = $transaction->user;
        $business = BusinessRegistration::where('user_id', $user->id)->first();

        $user->notify(new UserTransactionProcessedNotification($transaction, $business));
        // Receipt can be generated here or stored separately

        return response()->json([
            'success' => true,
            'message' => 'Transaction marked as processed'
        ]);
    }

    public function markPaymentReceived(Transaction $transaction)
    {
        // Authorize admin
        // abort_unless(auth()->user()->is_admin, 403);

        $receiptNo = $transaction->receipt_number ?? $this->functions->generateNewReceiptNo();

        // Update transaction status

        $transaction->status = 'processing';
        $transaction->payment_received_at = now();
        $transaction->receipt_number = $receiptNo;
        $transaction->receipt_generated_at = now();

        $transaction->save();

        // (optional) notify the user
        $user = $transaction->user;
        $business = BusinessRegistration::where('user_id', $user->id)->first();

        $user->notify(new UserPaymentReceivedNotification($transaction, $business));

        return response()->json(['success' => true]);
    }

    public function approve(Request $request, Transaction $transaction)
    {

        $transaction->load(['fromCurrency', 'toCurrency']);

        // return $transaction->fromCurrency;
        $response = ['success' => false, 'message' => ''];
        $validated = $request->validate([
            // 'official_rate' => 'required|numeric|min:0',
            'final_rate' => 'required|numeric|min:0',
            'final_amount_to_pay' => 'required|numeric|min:0',
            'invoice_expiry_minutes' => 'required|integer|min:1',
            'bank_name' => 'required|string',
            'account_number' => 'required|numeric',
            'account_name' => 'required|string',
        ]);

        DB::transaction(function () use ($transaction, $validated) {
            // generate invoice number if not exists
            $invoiceNumber = $transaction->invoice_number ??
                $this->functions->generateNewIncoiceId();

            // $official_rate = $validated['official_rate'];
            $official_rate = $this->functions->findLiveConversionRate($transaction->fromCurrency->code, $transaction->toCurrency->code);


            $transaction->official_rate = $official_rate;
            $transaction->final_rate = $validated['final_rate'];
            $transaction->final_amount_to_pay = $validated['final_amount_to_pay'];
            $transaction->invoice_expiry_minutes = $validated['invoice_expiry_minutes'];
            $transaction->bank_name = $validated['bank_name'];
            $transaction->account_number = $validated['account_number'];
            $transaction->account_name = $validated['account_name'];
            $transaction->status = 'pending_payment';
            $transaction->invoice_number = $invoiceNumber;
            $transaction->invoice_expires_at = now()->addMinutes((float) $validated['invoice_expiry_minutes']);
            $transaction->save();


            // Send invoice notification with countdown link
            $transaction->user->notify(new TransactionInvoiceNotification($transaction));
        });

        $response['success'] = true;
        $response['message'] = 'Transaction is approved & invoice has been generated';

        return back()->with('data', $response);
    }


    public function approvalData(Transaction $transaction)
    {
        // default exchange rate for this pair
        $rate = ExchangeRate::where('from_currency_id', $transaction->from_currency_id)
            ->where('to_currency_id', $transaction->to_currency_id)
            ->first();

        // settings defaults
        $defaults = [
            'invoice_expiry_minutes' => Setting::where('key', 'invoice_expiry_minutes')->value('value') ?? 30,
            'bank_name' => Setting::where('key', 'bank_name')->value('value'),
            'account_number' => Setting::where('key', 'account_number')->value('value'),
            'account_name' => Setting::where('key', 'account_name')->value('value'),
        ];

        return response()->json([
            'transaction' => $transaction->load('fromCurrency', 'toCurrency', 'user'),
            'rate' => $rate?->rate,
            'defaults' => $defaults
        ]);
    }










    public function viewAllTransactions(Request $request)
    {
        $length = $request->query('length', 10);

        // return $request->query('status');


        $transactions = Transaction::with('user', 'business', 'fromCurrency', 'toCurrency')
            ->addSelect('transactions.*')
            ->where('id', '!=', 0)
            ->filterStatus($request->query('status'))
            ->filterBusinessName($request->query('business_name'))
            ->filterUserName($request->query('user_name'))
            ->filterEmail($request->query('email'))
            ->filterFullName($request->query('full_name'))
            ->filterTransId($request->query('trans_id'))
            ->filterAmountToReceive($request->query('amount_to_receive'))
            ->filterAmountToPay($request->query('amount_to_pay'))
            ->filterFinalAmountToPay($request->query('final_amount_to_pay'))
            ->filterFromCurrency($request->query('from_currency'))
            ->filterToCurrency($request->query('to_currency'))
            ->filterDate($request->query('date'))
            ->filterBetweenDates($request->query('start_date'), $request->query('end_date'))
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
