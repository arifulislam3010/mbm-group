<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cv')->nullable();
            $table->string('photo_name')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();
            $table->string('spouse_name')->nullable();
            $table->string('gender')->nullable();
            $table->longText('social')->nullable();
            $table->longText('area_of_experiences')->nullable();
            $table->longText('employeements_history')->nullable();
            $table->string('profession_area')->nullable();
            $table->string('institution')->nullable();
            $table->string('edu_institution')->nullable();
            $table->string('designation')->nullable();
            $table->string('education_status')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('contact_number')->nullable();
            $table->mediumText('about')->nullable();
            $table->string('dob')->nullable();
            $table->string('nid')->nullable();
            $table->string('passport')->nullable();
            $table->string('tin')->nullable();
            $table->mediumText('address')->nullable();
            $table->string('google_location')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->integer('education_level_id')->nullable()->unsigned();
            $table->integer('degree_id')->nullable()->unsigned();
            $table->integer('sub_districts')->nullable()->unsigned();
            $table->integer('profession')->nullable()->unsigned();
            $table->integer('user_id')->nullable()->unsigned();
            $table->integer('created_by')->nullable()->unsigned();
            $table->integer('updated_by')->nullable()->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('user_infos', function(Blueprint $table){
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_infos');
    }
}
