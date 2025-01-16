<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User making the transaction
            $table->string('currency'); // Cryptocurrency (e.g., BTC)
            $table->decimal('amount'); // Cryptocurrency amount
            $table->decimal('fiat_amount'); // Equivalent fiat amount in USD
            $table->string('transaction_status')->default('pending'); // Status of the transaction
            $table->string('payin_address'); // Payment address
            $table->string('status')->default('pending'); // General status (e.g., completed, canceled)
            $table->dateTime('date'); // Date of the transaction
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
};
