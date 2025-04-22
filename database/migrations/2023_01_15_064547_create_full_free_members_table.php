<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFullFreeMembersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('full_free_members', function (Blueprint $table) {
            $table->id();
            $table->string('membership_code');
            $table->unsignedBigInteger('user_id');
            $table->string('status')->nullable();
            $table->text('decline_reason')->nullable();
            $table->unsignedBigInteger('year');
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
        Schema::dropIfExists('full_free_members');
    }
}
