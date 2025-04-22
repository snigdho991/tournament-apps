<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertStatusRetiresToLeaguesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->json('group_one_status')->nullable()->after('group_one_results');
            $table->json('group_two_status')->nullable()->after('group_two_results');
            $table->json('group_three_status')->nullable()->after('group_three_results');
            $table->json('group_four_status')->nullable()->after('group_four_results');
            $table->json('group_five_status')->nullable()->after('group_five_results');

            $table->json('group_one_retires')->nullable()->after('group_one_results');
            $table->json('group_two_retires')->nullable()->after('group_two_results');
            $table->json('group_three_retires')->nullable()->after('group_three_results');
            $table->json('group_four_retires')->nullable()->after('group_four_results');
            $table->json('group_five_retires')->nullable()->after('group_five_results');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('leagues', function (Blueprint $table) {
            //
        });
    }
}
