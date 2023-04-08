<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConferenceToolsDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conference_tools_details', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('syllabus_id')->unsigned()->nullable();
            $table->string('app_type');
            $table->string('meeting_id');
            $table->string('meeting_password');           
            $table->string('api_key');
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();           
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
        Schema::dropIfExists('conference_tools_details');
    }
}
