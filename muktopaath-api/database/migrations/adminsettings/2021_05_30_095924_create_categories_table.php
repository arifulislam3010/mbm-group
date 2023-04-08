<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dharmendra', function (Blueprint $table) {
           $table->increments('id');
            $table->string('title');
            $table->string('bn_title');
            $table->string('image')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->boolean('favourite')->nullable();
            $table->integer('order_number')->nullable();
            $table->integer('parent_id')->nullable()->unsigned();
            $table->integer('created_by')->nullable()->unsigned();
            $table->integer('updated_by')->nullable()->unsigned();
            $table->softDeletes();
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
        Schema::dropIfExists('categories');
    }
}
