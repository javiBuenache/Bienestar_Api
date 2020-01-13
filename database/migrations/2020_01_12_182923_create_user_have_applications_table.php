<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserHaveApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_have_applications', function (Blueprint $table) 
        {
            $table->primary(['user_id','app_id','date']);

            $table->unsignedInteger('user_id');
            $table->unsignedInteger('app_id');

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('app_id')->references('id')->on('applications')->onDelete('cascade');

            $table->dateTime('date'); 
            $table->string('event');           
            $table->double('latitude', 8, 6);
            $table->double('longitude', 8, 6);
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
        Schema::dropIfExists('user_have_applications');
    }
}
