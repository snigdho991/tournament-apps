<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRankingsTypeToSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('rankings_type')->nullable();
            $table->timestamp('rankings_last_updated')->nullable();
            $table->enum('publish_button_status', ['Locked', 'Unlocked'])->default('Locked');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('rankings_type');
            $table->dropColumn('rankings_last_updated');
            $table->dropColumn('publish_button_status');
        });
    }
}
