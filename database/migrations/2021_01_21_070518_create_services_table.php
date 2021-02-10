<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('test_code')->nullable();
            $table->string('test_name');
            $table->double('test_price');
            $table->text('test_constituents')->nullable();
            $table->integer('test_category_id')->nullable();
            $table->string('test_category')->nullable();
            $table->text('test_prerequisites')->nullable();
            $table->string('test_report_availability')->nullable();
            $table->text('test_desc')->nullable();
            $table->boolean('home');
            $table->string('creator_job_id');
            $table->string('status');
            $table->string('image_path');
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
        Schema::dropIfExists('services');
    }
}
