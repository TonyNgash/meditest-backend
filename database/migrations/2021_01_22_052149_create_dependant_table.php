<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDependantTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dependant', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('sirname')->nullable();
            $table->string('last_name');
            $table->string('gender');
            $table->text('address');
            $table->date('dob');
            $table->integer('user_id');
            $table->string('phone')->nullable();
            $table->string('relationship')->nullable;
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
        Schema::dropIfExists('dependant');
    }
}
