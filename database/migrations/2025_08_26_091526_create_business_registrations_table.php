<?php

use App\Models\Country;
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
        Schema::create('business_registrations', function (Blueprint $table) {
            $table->id();

            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade')->nullable();
            $table->string('business_name')->nullable();
            $table->foreignIdFor(Country::class)->constrained()->onDelete('cascade')->nullable();
            $table->foreignId('state_id')->constrained(
                table: 'inec_states',
            )->onDelete('cascade')->nullable();
            $table->foreignId('city_id')->constrained(
                table: 'inec_lgas',
            )->onDelete('cascade')->nullable();
            // $table->foreignId('ward_id')->nullable()->constrained(
            //     table: 'inec_wards',
            // )->onDelete('cascade');

            $table->text('address')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->text('certificate')->nullable();
            $table->text('moa')->nullable();
            $table->text('cosc')->nullable();
            $table->string('valid_id_type')->nullable();
            $table->string('valid_id')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_registrations');
    }
};
