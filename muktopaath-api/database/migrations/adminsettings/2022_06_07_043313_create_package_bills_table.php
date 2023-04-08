<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackageBillsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('package_bills', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->nullable();
            $table->bigInteger('package_id')->unsigned()->nullable();
            $table->bigInteger('owner_id')->unsigned()->nullable();
            $table->dateTime('month')->nullable();
            $table->double('amount');
            $table->tinyInteger('payment_status')->default(0);
            $table->dateTime('payment_date')->nullable();
            $table->foreign('package_id')->references('id')->on('packages');
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
        Schema::dropIfExists('package_bills');
    }
}
