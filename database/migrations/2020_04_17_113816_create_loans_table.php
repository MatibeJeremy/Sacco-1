<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            // loan disbursement authorization details
            $table->unsignedBigInteger('authorizing_user_id');
            $table->foreign('authorizing_user_id')
                ->references('id')
                ->on('users');

            // loan details
            $table->float('amount_issued');
            $table->float('amount_payable');
            $table->string('status');
            $table->boolean('is_active');
            $table->dateTime('matures_on');

            // loan disbursement details
            $table->dateTime('disbursed_on');
            $table->boolean('is_disbursed');

            // loan request details
            $table->unsignedBigInteger('loan_request_id');
            $table->foreign('loan_request_id')
                ->references('id')
                ->on('loan_requests');

            // loan recipient details
            $table->unsignedBigInteger('recipient_user_id');
            $table->foreign('recipient_user_id')
                ->references('id')
                ->on('users');
            $table->unsignedBigInteger('recipient_account_id');
            $table->foreign('recipient_account_id')
                ->references('id')
                ->on('accounts');

            // loan payment details
            $table->boolean('is_settled');
            $table->dateTime('settled_on');

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
        Schema::dropIfExists('loans');
    }
}
