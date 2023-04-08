<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unsigned();
            $table->string('title');
            $table->integer('file_id')->nullable();
            $table->text('summary')->nullable();
            $table->text('details')->nullable();
            $table->text('features')->nullable();
            $table->string('package_type')->nullable();
            $table->integer('limit_user')->nullable();
            $table->integer('product_limit')->nullable();
            $table->string('type')->nullable();
            $table->string('price_type')->nullable();
            $table->double('price')->nullable();

            $table->foreign('product_id')->references('id')->on('products');

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
        Schema::dropIfExists('packages');
    }
}
