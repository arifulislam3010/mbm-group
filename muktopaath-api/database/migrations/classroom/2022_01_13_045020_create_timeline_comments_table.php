<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimelineCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timeline_comments', function (Blueprint $table) {
            $table->id();
            $table->text('comment');
            $table->integer('created_by')->unsigned()->nullable();
            $table->bigInteger('timeline_id')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::table('timeline_comments', function(Blueprint $table){
            $table->foreign('timeline_id')->references('id')->on('timelines')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timeline_comments');
    }
}
