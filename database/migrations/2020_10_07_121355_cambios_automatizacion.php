<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CambiosAutomatizacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('companies', function (Blueprint $table) {
            $table->string('location')->default('Barcelona')->nullable();
        });

        Schema::table('agreements', function (Blueprint $table) {
            $table->unsignedInteger('days_of_holidays')->default(30);
            $table->char("holidays_type")->default("n");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
