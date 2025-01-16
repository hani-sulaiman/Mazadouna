<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('auctions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('starting_price');
            $table->text('description')->nullable();
            $table->text('thumbnail_image'); 
            $table->json('more_images'); 
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->decimal('min_increment');
            $table->string('shipping_details');
            $table->string('product_type');
            $table->json('tags')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // المستخدم الذي أنشأ المزاد
            $table->enum('status', ['pending', 'approved', 'ongoing', 'completed'])->default('pending');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('auctions');
    }
};
