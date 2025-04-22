<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertAutoSelectionToTournamentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tournaments', function (Blueprint $table) {
            $table->json('round_one_auto_selection')->nullable()->after('round_one_results');
            $table->json('round_two_auto_selection')->nullable()->after('round_two_results');
            $table->json('round_three_auto_selection')->nullable()->after('round_three_results');
            $table->json('quarter_final_auto_selection')->nullable()->after('quarter_final_results');
            $table->json('semi_final_auto_selection')->nullable()->after('semi_final_results');
            $table->json('final_auto_selection')->nullable()->after('final_results');
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
