<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHolidaysResponsibles extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->unsignedBigInteger("holiday_responsible")->nullable();
            $table->foreign("holiday_responsible")->references("id")->on("users");
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedBigInteger("holiday_responsible")->nullable();
            $table->foreign("holiday_responsible")->references("id")->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workers', function (Blueprint $table) {
            //
        });
        Schema::table('companies', function (Blueprint $table) {
            //
        });
    }
}
