<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('username')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password', 60)->nullable();
            $table->string('provider_id')->nullable();
            $table->string('provider')->nullable();
            $table->string('token')->nullable();
            $table->boolean('type')->default(0)->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamp('last_login_time')->nullable();
            $table->string('last_login_ip_address')->nullable();
            $table->tinyInteger('login_status')->nullable();
            $table->tinyInteger('completeness')->nullable();
            $table->string('phone2')->nullable();
            $table->string('email2')->nullable();
            $table->string('verify_token')->nullable();
            $table->tinyInteger('verify_status')->default(0);
            $table->string('verify_token2')->nullable();
            $table->tinyInteger('verify_status2')->default(0);
            $table->string('verify_token_phone')->nullable();
            $table->tinyInteger('verify_status_phone')->default(0);
            $table->string('verify_token_phone2')->nullable();
            $table->tinyInteger('verify_status_phone2')->default(0);
            $table->string('password_reset_token')->nullable();
            $table->boolean('verify')->default(0)->nullable();
            $table->boolean('old_user')->default(0)->nullable();
            $table->integer('old_user_pk')->nullable();
            $table->boolean('old_user_status')->default(1)->nullable();
            $table->integer('old_user_points')->default(0);
            $table->softDeletes();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
