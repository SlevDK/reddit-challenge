<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateThreadTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('thread_tag', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('thread_id')->unsigned();
            $table->bigInteger('tag_id')->unsigned();
            $table->timestamps();

            $table->foreign('thread_id')->references('id')->on('threads');
            $table->foreign('tag_id')->references('id')->on('tags');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('thread_tag');
    }
}
