<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
            $table->unsignedBigInteger('facebook_id')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password')->nullable();
            $table->string('gender')->nullable();
            $table->string('profile_img')->nullable();
            $table->string('interested_in_gender')->nullable();
            $table->string('birth_date')->nullable();
            $table->string('bio')->nullable();
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
        if (Schema::hasTable('reset_passwords')) {
            Schema::table('reset_passwords', function (Blueprint $table) {
                $table->dropForeign('reset_passwords_user_id_foreign');
            });
        } elseif (Schema::hasTable('password_resets')) {
            Schema::table('password_resets', function (Blueprint $table) {
                $table->dropForeign('password_resets_user_id_foreign');
            });
        }

        Schema::drop('users');
    }
}
