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
            $table->integer('memberId') ->unsigned();
            $table->foreign('memberId')->references('id')->on('members');
            $table->text('summary',50);
            $table->text('introduction',255);
            $table->string('appIcon');
            $table->integer('screenShotId')->unsigned();
            $table->foreign('screenShotId')->references('id')->on('app_imgs');
            $table->integer('categoryId')->unsigned();
            $table->foreign('categoryId')->references('id')->on('categories');
            $table->text('tags',20);
            $table->string('device',10);
            $table->string('version',20);
            $table->text('changelog',255);
            $table->string('fileURL');
            $table->integer('downloadTimes') ->unsigned();
            $table->tinyInteger('verify')->default(3);
            $table->tinyInteger('promotion')->default(0);
            $table->boolean('right')->default(1);
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
        Schema::dropIfExists('apps');
    }
}
