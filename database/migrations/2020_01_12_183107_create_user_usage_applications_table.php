<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserUsageApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_usage_applications', function (Blueprint $table) 
        {
            $table->primary(['user_id','app_id']);
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('app_id');

            
            $table->dateTime('max_time');
            $table->dateTime('start_time');
            $table->dateTime('finish_time');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('app_id')->references('id')->on('applications')->onDelete('cascade');
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
        Schema::dropIfExists('user_usage_applications');
    }
}
