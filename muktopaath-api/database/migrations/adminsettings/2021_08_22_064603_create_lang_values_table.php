<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLangValuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lang_values', function (Blueprint $table) {
            $table->id();
            $table->Biginteger('language_id')->unsigned()->nullable();
            $table->string('universal')->nullable();
            $table->string('value')->nullable();
            $table->timestamps();
        });

        Schema::table('lang_values', function(Blueprint $table){
            $table->foreign('language_id')->references('id')->on('languages')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lang_values');
    }
}
