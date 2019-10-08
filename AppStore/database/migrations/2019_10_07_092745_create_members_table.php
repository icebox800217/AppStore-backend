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
            $table->increments('id');
            $table->string('name',50);
            $table->string('phone',10)->unique();
            $table->string('email',100)->unique();
            $table->string('idNumber',10)->unique();
            $table->string('password',255);
            $table->tinyInteger('level')->default(1);
            $table->boolean('verify')->nullable();
            $table->boolean('right')->default(1);
            $table->integer('imgId')->unsigned()->default(1);
            $table->foreign('imgId')->references('id')->on('member_imgs');
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
        Schema::dropIfExists('members');
    }
}
