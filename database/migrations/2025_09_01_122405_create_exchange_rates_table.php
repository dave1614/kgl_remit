<?php

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
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_currency_id')->constrained(
                table: 'currencies',
            )->onDelete('cascade')->nullable();
            $table->foreignId('to_currency_id')->constrained(
                table: 'currencies',
            )->onDelete('cascade')->nullable();
            $table->decimal('rate', 15, 6); // high precision
            $table->string('note')->nullable();
            $table->timestamps();

            $table->unique(['from_currency_id', 'to_currency_id']); // prevent duplicate pairs
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};
