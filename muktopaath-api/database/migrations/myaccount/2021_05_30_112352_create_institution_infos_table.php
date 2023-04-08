<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstitutionInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('institution_infos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('institution_name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->integer('institution_type')->unsigned()->nullable();
            $table->string('username')->unique();
            $table->string('office_chief')->nullable();
            $table->string('designation')->nullable();
            $table->string('address')->nullable();
            $table->string('google_location')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('contact_person_mobile')->nullable();
            $table->string('contact_person_email')->nullable();
            $table->longText('initial')->nullable();
            $table->longText('metadata')->nullable();
            $table->longText('social')->nullable();
            $table->longText('contacts')->nullable();
            $table->longText('credit')->nullable();
            $table->longText('heading')->nullable();
            $table->longText('subscription')->nullable();
            $table->string('website')->nullable();
            $table->string('legal_document_file')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->tinyInteger('type')->nullable();
            $table->integer('created_by')->unsigned()->nullable();
            $table->integer('updated_by')->unsigned()->nullable();
            $table->integer('user_id')->unsigned()->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
        Schema::table('institution_infos', function(Blueprint $table){
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
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
        Schema::dropIfExists('institution_infos');
    }
}
