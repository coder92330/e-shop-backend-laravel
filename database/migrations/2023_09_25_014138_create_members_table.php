<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('members', function (Blueprint $table) {
            $table->timestamps();
            $table->increments('id');
            $table->string('name')->unique();
            $table->string('password');
            $table->Integer('level_id');
            $table->String('last_login')->default("");
            $table->String('parent')->default("");
            $table->String('id_path')->default("");
            $table->rememberToken();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
}
