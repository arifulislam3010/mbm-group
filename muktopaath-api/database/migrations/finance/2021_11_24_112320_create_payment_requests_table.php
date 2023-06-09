<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('balance_id')->unsigned();
            $table->bigInteger('request_amount')->unsigned()->nullable();
            $table->integer('status');
            $table->integer('requested_by')->unsigned();
            $table->integer('granted_by')->unsigned();
            $table->timestamps();
            
        });

        Schema::table('payment_requests', function(Blueprint $table){
            $table->foreign('balance_id')->references('id')->on('balances')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_requests');
    }
}