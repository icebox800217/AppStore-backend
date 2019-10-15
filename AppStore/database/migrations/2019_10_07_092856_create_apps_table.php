<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('apps', function (Blueprint $table) {
            $table->increments('id');
            $table->string('appName',50);
            $table->integer('memberId')->unsigned();
            $table->foreign('memberId')->references('id')->on('members');
            $table->string('summary',50);
            $table->string('introduction',255);
            $table->string('appIcon');
            $table->integer('categoryId')->unsigned();
            $table->foreign('categoryId')->references('id')->on('categories');
            $table->string('tags',20);
            $table->string('device',10);
            $table->string('version',20);
            $table->string('changelog',255);
            $table->string('fileURL')->unique();
            $table->integer('downloadTimes') ->unsigned()->default(0);
            $table->tinyInteger('verify')->default(3);
            $table->tinyInteger('promotion')->default(0);
            $table->boolean('permission')->default(1);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('apps');
    }
}
