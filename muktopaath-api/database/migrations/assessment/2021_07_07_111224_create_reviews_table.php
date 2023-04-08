<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->integer('rating')->default(0);
            $table->longText('review')->nullable();
            $table->Integer('course_enrollment_id')->unsigned();
            $table->Integer('course_batch_id')->unsigned();
            $table->foreign('course_enrollment_id')->references('id')->on('course_enrollments')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('course_batch_id')->references('id')->on('course_batches')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('reviews');
    }
}
