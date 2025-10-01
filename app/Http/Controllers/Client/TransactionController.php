<?php

namespace App\Http\Controllers\Client;

use App\Functions\UsefulFunctions;
use App\Http\Controllers\Controller;
use App\Models\BusinessRegistration;
use App\Models\Currency;
use App\Models\ExchangeRate;
use App\Models\Transaction;
use App\Notifications\AdminTransactionCreatedNotification;
use App\Notifications\AdminTransactionPaymentProofSentNotification;
use App\Notifications\TransactionCreatedNotification;
use App\Notifications\UserPaymentProofUploadedNotification;
use App\Notifications\UserTransactionRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

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
        return Inertia::render('Transaction/Index', $props);
    }

    public function viewAllTransactions(Request $request)
    {
        $length = $request->query('length', 10);

        // return $request->query('status');
        $user = Auth::user();


        $transactions = Transaction::with('user', 'business', 'fromCurrency', 'toCurrency')
            ->addSelect('transactions.*')
            ->where('user_id', $user->id)
            ->filterStatus($request->query('status'))

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



    public function showReceipt(Request $request, Transaction $transaction)
    {
        $user = $request->user();
        if ($user->is_admin == 0) {
            abort_unless($transaction->user_id === auth()->id(), 403);
        }

        $transaction->load(['fromCurrency', 'toCurrency', 'user']);
        $business = BusinessRegistration::where('user_id', $transaction->user_id)->first();

        return Inertia::render('Transaction/Receipt', [
            'transaction' => $transaction,
            'business' => $business,
            'APP_NAME' => config('app.name'),
            'now' => now(), // to start countdown
        ]);
    }


    public function showInvoice(Request $request, Transaction $transaction)
    {

        $user = $request->user();
        if ($user->is_admin == 0) {
            abort_unless($transaction->user_id === auth()->id(), 403);
        }

        $transaction->load('user', 'fromCurrency', 'toCurrency');
        // return $transaction;
        $business = BusinessRegistration::where('user_id', $transaction->user_id)->first();
        return Inertia::render('Transaction/Invoice', [
            'transaction' => $transaction,
            'business' => $business,
            'APP_NAME' => config('app.name'),
            'now' => now(), // to start countdown
        ]);
    }

    public function uploadProof(Request $request, Transaction $transaction)
    {
        $user = $request->user();

        $response = ['success' => false, 'message' => ''];
        $business = BusinessRegistration::where('user_id', $user->id)->first();
        // Authorise that current user owns this transaction
        abort_unless($transaction->user_id === auth()->id(), 403);

        $request->validate([
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png|max:4096',
        ], [
            'payment_proof.required' => 'Please select a payment proof file.',
            'payment_proof.mimes' => 'Proof must be a JPG, PNG file.',
            'payment_proof.max' => 'File must not exceed 4MB.',
        ]);

        $path = $request->file('payment_proof')->store('payment_proofs', 'public');


        $transaction->payment_proof_path = $path;
        $transaction->status = 'payment_proof_submitted';

        $transaction->save();


        // Notify admin
        $admin_user = $this->functions->getAdminUser();


        Notification::send($admin_user, new AdminTransactionPaymentProofSentNotification($transaction, $user, $business));

        $user->notify(new UserPaymentProofUploadedNotification($transaction, $user, $business));

        $response['success'] = true;
        $response['message'] = 'Payment proof uploaded successfully.';

        return back()->with('data', $response);
    }






    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $currencies = Currency::orderBy('code')->get();
        $exchangeRates = ExchangeRate::select('from_currency_id', 'to_currency_id', 'rate')->get();
        // return $currencies;
        // grab defaults from query string
        $defaults = [
            'from_currency_id' => $request->query('from_currency_id'),
            'to_currency_id'   => $request->query('to_currency_id'),
            'amount'           => $request->query('amount'), // optional
        ];

        return Inertia::render('Transaction/Create', [
            'currencies'    => $currencies,
            'exchangeRates' => $exchangeRates,
            'defaults'      => $defaults, // send to vue
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_currency_id' => 'required|exists:currencies,id',
            'to_currency_id'   => 'required|exists:currencies,id',
            'amount_to_receive' => 'required|numeric|min:0.01',
            'amount_to_pay'    => 'required|numeric|min:0.01'
        ]);

        $user = $request->user();

        $business = BusinessRegistration::where('user_id', $user->id)->first();

        $trans_id = $this->functions->generateNewTransactionId();
        $rate = ExchangeRate::where('from_currency_id', $request->from_currency_id)
            ->where('to_currency_id', $request->to_currency_id)
            ->first();

        // Create transaction in pending_request state
        $transaction = Transaction::create([
            'trans_id' => $trans_id,
            'user_id'           => $user->id,
            'business_id' =>    $business->id,
            'from_currency_id'  => $validated['from_currency_id'],
            'to_currency_id'    => $validated['to_currency_id'],
            'amount_to_receive' => $validated['amount_to_receive'],
            'amount_to_pay'     => $validated['amount_to_pay'],
            'status'            => 'pending_request',
            'first_rate' => $rate?->rate
            // invoice expiry will be set later by admin or a config default
        ]);

        // Notify user + admin
        $admin_user = $this->functions->getAdminUser();

        Notification::send($user, new TransactionCreatedNotification($transaction, $user));
        Notification::send($admin_user, new AdminTransactionCreatedNotification($transaction, $user, $business));

        // In controller
        return response()->json([
            'success' => true,
            'message' => 'Transaction request to admin submitted. You will be notified shortly.'
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        $transaction->load(['fromCurrency', 'toCurrency']); // eager load
        return response()->json($transaction);
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
