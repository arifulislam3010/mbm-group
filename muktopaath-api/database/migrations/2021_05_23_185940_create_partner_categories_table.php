<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePartnerCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('partner_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->integer('status')->nullable();
            $table->integer('parent_id')->nullable()->unsigned();
            $table->integer('owner_id')->unsigned();
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('partner_categories', function (Blueprint $table) {
            $table->foreign('parent_id')->references('id')->on('partner_categories')->onDelete('cascade')->onUpdate('cascade'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('partner_categories');
    }
}
