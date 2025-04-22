<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InsertSixTillTenStatusRetiresToLeaguesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('leagues', function (Blueprint $table) {
            $table->json('group_six_status')->nullable()->after('group_six_results');
            $table->json('group_seven_status')->nullable()->after('group_seven_results');
            $table->json('group_eight_status')->nullable()->after('group_eight_results');
            $table->json('group_nine_status')->nullable()->after('group_nine_results');
            $table->json('group_ten_status')->nullable()->after('group_ten_results');

            $table->json('group_six_retires')->nullable()->after('group_six_results');
            $table->json('group_seven_retires')->nullable()->after('group_seven_results');
            $table->json('group_eight_retires')->nullable()->after('group_eight_results');
            $table->json('group_nine_retires')->nullable()->after('group_nine_results');
            $table->json('group_ten_retires')->nullable()->after('group_ten_results');
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
