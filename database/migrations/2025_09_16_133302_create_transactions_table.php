<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->string('trans_id', 30)->unique(); // like TRX-202409-ABCD123
            $table->string('invoice_number', 30)->nullable()->unique();
            // who initiated
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade')->nullable();

            // if the user belongs to a business
            $table->foreignId('business_id')
                ->nullable()
                ->constrained('business_registrations')
                ->onDelete('cascade');

            // currencies
            $table->foreignId('from_currency_id')
                ->constrained('currencies')
                ->onDelete('cascade');

            $table->foreignId('to_currency_id')
                ->constrained('currencies')
                ->onDelete('cascade');

            // amounts
            $table->decimal('official_rate', 20, 6)->nullable(); //International rate
            $table->decimal('first_rate', 20, 6)->nullable(); // admin’s final rate
            $table->decimal('amount_to_receive', 20, 6); // amount beneficiary should get
            $table->decimal('amount_to_pay', 20, 6); // initial calculated amount
            $table->decimal('final_rate', 20, 6)->nullable(); // admin’s final rate
            $table->decimal('final_amount_to_pay', 20, 6)->nullable(); // admin’s final amount

            $table->integer('invoice_expiry_minutes')->nullable(); //ow much time in minutes it takes the invoice to expire;

            //Bank Details
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();



            // enum status — adjust list as you need
            $table->enum('status', [
                'pending_request',      // just submitted by user
                'pending_payment',      // invoice generated, waiting payment
                'payment_proof_submitted', // user uploaded proof
                'payment_verified',     // admin verified payment
                'processing',           // processing the transfer
                'completed',            // fully processed
                'rejected',             // admin rejected
                'expired',              // invoice expired
            ])->default('pending_request');
            // optional rejection reason
            $table->text('rejection_reason')->nullable();
            $table->timestamp('rejected_at')->nullable();

            // invoice expiry + proof upload
            $table->timestamp('invoice_expires_at')->nullable();
            $table->string('payment_proof_path')->nullable();


            $table->timestamp('payment_received_at')->nullable();

            $table->string('receipt_number')->nullable();
            $table->timestamp('receipt_generated_at')->nullable();

            // when fully processed
            $table->timestamp('processed_at')->nullable();
            $table->string('receipt_pdf_path')->nullable();



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

