<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserBoardRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_board_role', function (Blueprint $table) {
            $table->id();
            $table->string('role', 10);
            $table->bigInteger('board_id')->unsigned();
            $table->bigInteger('user_id')->unsigned();
            $table->timestamps();

            $table->foreign('board_id')->references('id')->on('boards');
            $table->foreign('user_id')->references('id')->on('users');
            $table->index(['user_id', 'board_id']);
            $table->index(['board_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_board_role');
    }
}
