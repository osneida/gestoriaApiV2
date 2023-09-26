<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddContractHolidays extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->unsignedInteger('days_of_holidays')->default(0);
            $table->char('holidays_type')->default('n');
            $table->string('holidays_location')->default('Barcelona')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->removeColumn("days_of_holidays");
            $table->removeColumn("holidays_type");
            $table->removeColumn("holidays_location");
        });
    }
}
