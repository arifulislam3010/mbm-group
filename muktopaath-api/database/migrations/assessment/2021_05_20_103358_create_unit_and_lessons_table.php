<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUnitAndLessonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('syllabuses', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description')->nullable();
            $table->tinyInteger('status')->default(1);
            // $table->string('tags')->nullable();
            $table->integer('order_number')->nullable();
            $table->integer('course_batch_id')->unsigned();
            $table->integer('parent_id')->nullable();
            $table->integer('learning_content_id')->unsigned();
            $table->string('content_title')->nullable();
            $table->string('content_type')->nullable();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();

            $table->foreign('course_batch_id')->references('id')->on('course_batches')->onDelete('cascade')->onUpdate('cascade');

            // $table->foreign('learning_content_id')->references('id')->on('learning_contents')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('unit_and_lessons');
    }
}
