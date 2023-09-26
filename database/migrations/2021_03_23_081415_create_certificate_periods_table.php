<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificatePeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificate_periods', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger("user_id");
            $table->foreign("user_id")->references("id")->on("users");

            $table->unsignedBigInteger("company_id");
            $table->foreign("company_id")->references("id")->on("companies");
            $table->string("period")->comment("Periodo procesado nóminas");

            $table->dateTime("processed")->nullable();
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
        Schema::dropIfExists('certificate_periods');
    }
}
