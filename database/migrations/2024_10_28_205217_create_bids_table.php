<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->foreignId('auction_user_id')->constrained('auctions_users')->onDelete('cascade'); // الإشارة إلى مزايد معين في مزاد
            $table->decimal('bid_value', 10, 2); // قيمة العرض
            $table->dateTime('bid_date'); // تاريخ العرض
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bids');
    }
};
