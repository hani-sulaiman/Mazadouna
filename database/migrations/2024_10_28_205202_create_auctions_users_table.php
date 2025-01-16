<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('auctions_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_id')->constrained()->onDelete('cascade'); // مزاد
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // مستخدم
            $table->enum('status', ['viewer', 'bidder']); // الحالة (مشاهد أو مزايد)
            $table->dateTime('join_date'); // تاريخ الانضمام
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('auctions_users');
    }
};
