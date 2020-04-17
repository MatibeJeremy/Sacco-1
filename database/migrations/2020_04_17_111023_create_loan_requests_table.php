<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_requests', function (Blueprint $table) {
            $table->id('id');

            // loan request details
            $table->float('amount_requested');
            $table->string('status');
            $table->boolean('is_approved');
            $table->unsignedBigInteger('recipient_user_id');
            $table->foreign('recipient_user_id')
                ->references('id')
                ->on('users');

            // loan request approval details
            $table->unsignedBigInteger('authorizing_user_id');
            $table->foreign('authorizing_user_id')
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
        Schema::dropIfExists('loan_requests');
    }
}
