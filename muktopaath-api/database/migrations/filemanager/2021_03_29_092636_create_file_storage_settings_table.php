<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFileStorageSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('file_storage_settings', function (Blueprint $table) {
            $table->id();
            $table->longText('info')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->integer('region_id')->nullable();
            $table->bigInteger('storage_type_id')->unsigned();
            $table->bigInteger('file_type_id')->unsigned();
            $table->foreign('storage_type_id')->references('id')->on('storage_types')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('file_type_id')->references('id')->on('file_types')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('file_storage_settings');
    }
}
