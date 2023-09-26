<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertificatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger("worker_id");
            $table->foreign("worker_id")->references("id")->on("workers");
            $table->unsignedBigInteger("company_id");
            $table->foreign("company_id")->references("id")->on("companies");
            $table->string("period")->comment("Periodo a procesar la nómina");

            $table->string("document_file");
            $table->dateTime("processed")->nullable();
            $table->dateTime("opened")->nullable();


            $table->unsignedTinyInteger("attempts")->default(0)->comment("Número de veces que se ha intentado enviar, report de error");
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
        Schema::dropIfExists('certificates');
    }
}
