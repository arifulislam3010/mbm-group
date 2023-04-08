<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContentBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('content_banks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('content_id')->nullable();
            $table->string('title');
            $table->integer('cat_id')->unsigned()->nullable();
            $table->string('type')->comments('image,video,audio,documents')->nullable();
            $table->string('file_name')->nullable();
            $table->string('file_encode_path')->nullable();
            $table->string('cover_thumb_img')->nullable();
            $table->string('tags')->nullable();
            $table->tinyInteger('license')->default('0');
            $table->mediumText('description')->nullable();
            $table->tinyInteger('shareable')->default('0');
            $table->tinyInteger('paid')->default('0');
            $table->integer('price')->default('0');
            $table->integer('size')->default('0');
            $table->tinyInteger('copy_protect')->default('0');
            $table->tinyInteger('status')->default('0');
            $table->integer('use_count')->default('0');
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('owner_id')->unsigned()->nullable();
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
        Schema::dropIfExists('content_banks');
    }
}
