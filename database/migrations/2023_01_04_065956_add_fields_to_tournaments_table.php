<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFieldsToTournamentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->json('round_one_deadline')->nullable();
            $table->json('round_one_matches')->nullable();
            $table->json('round_one_results')->nullable();
            
            $table->json('round_two_deadline')->nullable();
            $table->json('round_two_matches')->nullable();
            $table->json('round_two_results')->nullable();

            $table->json('round_three_deadline')->nullable();
            $table->json('round_three_matches')->nullable();
            $table->json('round_three_results')->nullable();

            $table->json('quarter_final_deadline')->nullable();
            $table->json('quarter_final_matches')->nullable();
            $table->json('quarter_final_results')->nullable();

            $table->json('semi_final_deadline')->nullable();
            $table->json('semi_final_matches')->nullable();
            $table->json('semi_final_results')->nullable();

            $table->json('final_deadline')->nullable();
            $table->json('final_matches')->nullable();
            $table->json('final_results')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tournaments', function (Blueprint $table) {
            //
        });
    }
}
