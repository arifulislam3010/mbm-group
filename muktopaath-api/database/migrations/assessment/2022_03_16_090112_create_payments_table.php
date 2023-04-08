<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->double('amount');
            $table->string('pm_number')->nullable();
            $table->Integer('course_id')->unsigned()->nullable();
            $table->Integer('course_batch_id')->unsigned()->nullable();
            $table->Integer('created_by')->unsigned()->nullable();
            $table->string('type')->nullable();
            $table->Integer('owner_id')->unsigned()->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('payments', function(Blueprint $table){
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('course_batch_id')->references('id')->on('course_batches')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
