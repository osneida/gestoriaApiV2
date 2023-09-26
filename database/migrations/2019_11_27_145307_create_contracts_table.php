<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("worker_id");
            $table->foreign("worker_id")->references("id")->on("workers");
            $table->unsignedBigInteger("company_id");
            $table->foreign("company_id")->references("id")->on("companies");
            $table->unsignedBigInteger("agreement_id")->nullable();
            $table->foreign("agreement_id")->references("id")->on("agreements");
            $table->unsignedBigInteger("category_id")->nullable();
            $table->foreign("category_id")->references("id")->on("categories");
            $table->string("document_identity_file_a")->nullable()->comment("Archivo documento 1");
            $table->string("document_identity_file_b")->nullable()->comment("Archivo documento 2");
            $table->string("nss")->comment("Número seguridad social");
            $table->string("nss_file")->nullable()->comment("Archivo documento seguridad social");
            $table->string("iban")->nullable();
            $table->enum("number_of_payments", [12, 14, 15])->default(12);
            $table->unsignedTinyInteger("contract_type")->comment("Tipo de contrato");
            $table->unsignedTinyInteger("working_day_type")->comment("Tipo de jornada");
            $table->date("contract_end_date")->nullable()->comment("Cuando finaliza el contrato");
            $table->string("contract_reason")->nullable()->comment("Motivo del cese de contrato");
            $table->date("contract_start_date")->nullable()->comment("Cuando empieza el contrato");
            $table->text("hours_worked_start")->nullable()->comment("Horas de inicio de jornada de lunes a domingo");
            $table->text("hours_worked_end")->nullable()->comment("Horas de fin de jornada de lunes a domingo");
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
        Schema::dropIfExists('contracts');
    }
}
