<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('booking', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->boolean('self');
            $table->boolean('paid');
            $table->date('scheduled_date')->nullable();
            $table->time('scheduled_time')->nullable();
            $table->double('total_amount');
            $table->boolean('old_new')->default(true);
            $table->boolean('seen')->default(false);
            $table->string('status')->default('pending');
            $table->string('phone')->nullable();
            $table->text('address_desc')->nullable();
            $table->string('lat');
            $table->string('lon');
            $table->text('locality')->nullable();
            $table->text('admin_area')->nullable();
            $table->text('sub_admin_area')->nullable();
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
        Schema::dropIfExists('booking');
    }
}
