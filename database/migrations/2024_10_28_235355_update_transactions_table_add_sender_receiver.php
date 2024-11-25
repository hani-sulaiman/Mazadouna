<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Drop the existing `user_id` column if it exists
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            // Add the new `sender_id` and `receiver_id` columns
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade'); // Sender of the transaction
            $table->foreignId('receiver_id')->nullable()->constrained('users')->onDelete('set null'); // Receiver of the transaction
        });
    }

    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Rollback changes by dropping `sender_id` and `receiver_id`
            $table->dropForeign(['sender_id']);
            $table->dropForeign(['receiver_id']);
            $table->dropColumn(['sender_id', 'receiver_id']);

            // Re-add the original `user_id` column if rolling back
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        });
    }
};
