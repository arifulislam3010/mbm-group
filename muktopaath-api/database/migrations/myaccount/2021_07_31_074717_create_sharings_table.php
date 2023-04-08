<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSharingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sharings', function (Blueprint $table) {
            $table->id();
            $table->string('db_con_name')->nullable();
            $table->string('table_name')->nullable();
            $table->integer('table_id')->unsigned();
            $table->longText('activity')->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->tinyInteger('status')->default(0)->comment = '0 - in list, 1 - accepted, 2 - rejected';
            $table->timestamps();
        });
 
        Schema::table('sharings', function(Blueprint $table){
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sharings');
    }
}
