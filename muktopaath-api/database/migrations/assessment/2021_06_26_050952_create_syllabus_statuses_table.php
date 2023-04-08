<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSyllabusStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('syllabus_statuses', function (Blueprint $table) {
            $table->id();
            $table->integer('course_enrollment_id')->unsigned()->nullable();
            $table->integer('course_batch_id')->unsigned()->nullable();
            $table->bigInteger('syllabus_id')->unsigned()->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::table('syllabus_statuses', function(Blueprint $table){
            $table->foreign('course_enrollment_id')->references('id')->on('course_enrollments')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('course_batch_id')->references('id')->on('course_batches')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('syllabus_id')->references('id')->on('syllabuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('syllabus_statuse');
    }
}
