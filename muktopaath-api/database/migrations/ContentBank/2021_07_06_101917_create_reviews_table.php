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
            $table->bigInteger('order_content_id')->unsigned();
            $table->bigInteger('learning_content_id')->unsigned();
            $table->foreign('order_content_id')->references('id')->on('order_contents')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('learning_content_id')->references('id')->on('learning_contents')->onDelete('cascade')->onUpdate('cascade');
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
