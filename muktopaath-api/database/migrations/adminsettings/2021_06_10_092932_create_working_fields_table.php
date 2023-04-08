<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkingFieldsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('working_fields', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('bn_title');
            $table->string('status')->nullable();
            $table->integer('order_number')->nullable();
            $table->unsignedInteger('profession_id')->nullable();
            $table->unsignedInteger('created_by')->nullable();
            $table->unsignedInteger('updated_by')->nullable();
            $table->timestamps();
        });
         Schema::table('working_fields', function(Blueprint $table){
            $table->foreign('profession_id')->references('id')->on('professions')->onDelete('set null')->onUpdate('cascade');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('working_fields');
    }
}
