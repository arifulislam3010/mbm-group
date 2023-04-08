<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserFilestorageSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_filestorage_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id')->unsigned();
            $table->longText('credentials')->nullable();
            $table->bigInteger('storage_type_id')->unsigned();
            $table->bigInteger('file_type_id')->unsigned();
            $table->foreign('storage_type_id')->references('id')->on('storage_types')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('file_type_id')->references('id')->on('file_types')->onDelete('cascade')->onUpdate('cascade');
            $table->tinyInteger('status')->default(0);
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
        Schema::dropIfExists('user_filestorage_settings');
    }
}
