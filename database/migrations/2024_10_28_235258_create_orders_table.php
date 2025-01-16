<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained()->onDelete('cascade'); // ID of the auction
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // ID of the winning user
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null'); // Associated transaction
            $table->string('status')->default('pending'); // Status of the order
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
