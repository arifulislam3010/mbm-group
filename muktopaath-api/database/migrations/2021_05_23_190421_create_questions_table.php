<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('questions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->string('title_content_type')->nullable();
            $table->integer('title_content_id')->nullable()->unsigned();
            $table->string('title_content_url')->nullable();
            $table->integer('dif_level')->nullable();
            $table->mediumText('description')->nullable();
            $table->string('type');
            $table->mediumText('details')->nullable()->comments('question details of image, short answer and paragraph');
            $table->mediumText('options')->nullable()->comments('question details of image, short answer and paragraph');
            $table->string('answer')->nullable()->comments('question answer of image, short answer and paragraph');
            $table->string('time')->nullable();
            $table->string('date')->nullable();
            $table->string('status')->nullable();
            $table->integer('mark')->default(1);
            $table->integer('partner_category')->nullable()->unsigned();
            $table->integer('created_by')->unsigned();
            $table->integer('updated_by')->unsigned();
            $table->integer('owner_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
        
        Schema::table('questions', function(Blueprint $table){
            $table->foreign('partner_category')->references('id')->on('partner_categories')->onDelete('set null')->onUpdate('cascade');
        });

    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('questions');
    }
}
