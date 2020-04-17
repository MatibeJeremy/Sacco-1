<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('id');
            $table->uuid('uuid');
            $table->float('amount');
            $table->string('type');
            $table->string('status');
            $table->unsignedBigInteger('sender_account_id');
            $table->unsignedBigInteger('recipient_account_id');
            $table->foreign('sender_account_id')
                ->references('id')
                ->on('accounts');
            $table->foreign('recipient_account_id')
                ->references('id')
                ->on('accounts');
            $table->unsignedBigInteger('sender_user_id');
            $table->unsignedBigInteger('recipient_user_id');
            $table->foreign('sender_user_id')
                ->references('id')
                ->on('users');
            $table->foreign('recipient_user_id')
                ->references('id')
                ->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
