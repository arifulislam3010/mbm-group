<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLearningContentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('learning_contents', function (Blueprint $table) {
            $table->id();
            $table->string('content_type')->nullable();
            $table->string('title')->nullable();
            $table->mediumText('description')->nullable();
            $table->integer('duration')->nullable();
            $table->tinyInteger('forward')->nullable();
            $table->tinyInteger('forwardable')->nullable();
            $table->tinyInteger('allow_preview')->nullable();
            $table->longText('more_data_info')->nullable();
            $table->tinyInteger('quiz')->default(0);
            $table->mediumText('quiz_data')->nullable();
            $table->mediumText('quiz_marks')->nullable();
            $table->integer('content_id')->nullable();
            $table->integer('owner_id')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            
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
        Schema::dropIfExists('learning_contents');
    }
}
