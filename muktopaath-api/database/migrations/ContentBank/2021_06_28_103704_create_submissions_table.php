<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubmissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('submission_type');
            $table->integer('user_id')->unsigned();
            $table->bigInteger('order_content_id')->unsigned();
            $table->longText('submitted_answers')->nullable();
            $table->double('marks')->nullable();
            $table->timestamps();
        });
        Schema::table('submissions', function(Blueprint $table){
            $table->foreign('order_content_id')->references('id')->on('order_contents');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('submissions');
    }
}
