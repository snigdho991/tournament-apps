<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRankingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rankings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->year('year')->nullable();
            
            $table->unsignedBigInteger('1st_elite')->nullable();
            $table->unsignedBigInteger('1st_pro')->nullable();
            $table->unsignedBigInteger('1st_adv')->nullable();
            $table->unsignedBigInteger('1st_int')->nullable();
            $table->unsignedBigInteger('1st_rookie')->nullable();

            $table->unsignedBigInteger('2nd_elite')->nullable();
            $table->unsignedBigInteger('2nd_pro')->nullable();
            $table->unsignedBigInteger('2nd_adv')->nullable();
            $table->unsignedBigInteger('2nd_int')->nullable();
            $table->unsignedBigInteger('2nd_rookie')->nullable();

            $table->unsignedBigInteger('3rd_elite')->nullable();
            $table->unsignedBigInteger('3rd_pro')->nullable();
            $table->unsignedBigInteger('3rd_adv')->nullable();
            $table->unsignedBigInteger('3rd_int')->nullable();
            $table->unsignedBigInteger('3rd_rookie')->nullable();

            $table->unsignedBigInteger('4th_elite')->nullable();
            $table->unsignedBigInteger('4th_pro')->nullable();
            $table->unsignedBigInteger('4th_adv')->nullable();
            $table->unsignedBigInteger('4th_int')->nullable();
            $table->unsignedBigInteger('4th_rookie')->nullable();

            $table->unsignedBigInteger('1st_league')->nullable();
            $table->unsignedBigInteger('2nd_league')->nullable();
            $table->unsignedBigInteger('top16_finals')->nullable();

            $table->enum('status', ['Active', 'Disabled'])->default('Active');

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
        Schema::dropIfExists('rankings');
    }
}
