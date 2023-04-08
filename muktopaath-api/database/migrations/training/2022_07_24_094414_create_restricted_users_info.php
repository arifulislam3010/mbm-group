<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRestrictedUsersInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('restricted_users_info', function (Blueprint $table) {
            $table->id();
            $table->string('designation')->nullable();
            $table->string('institution')->nullable();
            $table->text('address')->nullable();
            $table->bigInteger('restricted_user_id')->unsigned();
            $table->timestamps();
        });

        Schema::table('restricted_users_info', function(Blueprint $table){
            $table->foreign('restricted_user_id')->references('id')->on('restricted_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('restricted_users_info');
    }
}
