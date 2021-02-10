<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePhlebosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('phlebos', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('sirname')->nullable();
            $table->string('last_name');
            $table->string('gender');
            $table->text('qualifications');
            $table->string('email');
            $table->string('phone');
            $table->string('password');
            $table->string('dob');
            $table->rememberToken()->nullable();
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
        Schema::dropIfExists('phlebos');
    }
}
