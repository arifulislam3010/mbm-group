<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDegreesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            Schema::create('degrees', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->tinyInteger('weight')->nullable();
            $table->tinyInteger('order')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->integer('education_level_id')->unsigned()->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->timestamps();
        });
            
        Schema::table('degrees', function(Blueprint $table){
            $table->foreign('education_level_id')->references('id')->on('education_levels')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('degrees');
    }
}
