<?php

use App\Models\Country;
use App\Models\InecLga;
use App\Models\InecState;
use App\Models\InecWard;
use App\Models\State;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_admin')->default(false);


            $table->string('user_name');
            $table->string('slug');

            $table->string('name');
            $table->string('email')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->boolean('email_enabled')->default(true);
            $table->boolean('sms_enabled')->default(true);
            $table->timestamp('last_activity')->nullable();

            $table->text('providus_account_number')->nullable();
            $table->text('providus_account_name')->nullable();
            $table->text('bvn')->nullable();


            $table->string('phone_code')->nullable();
            $table->string('phone')->nullable();
            $table->foreignIdFor(Country::class)->constrained()->onDelete('cascade')->nullable();

            // $table->foreignIdFor(State::class)->constrained()->onDelete('cascade')->nullable();

            $table->boolean('business_registered')->default(false);
            $table->timestamp('business_registered_at')->nullable();

            $table->boolean('created')->default(false);
            $table->string('created_date')->nullable();
            $table->string('created_time')->nullable();


            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('recepient_code')->nullable();


            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
