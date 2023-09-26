<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('workers', function (Blueprint $table) {
            $table->bigIncrements('id');
            //$table->unsignedBigInteger("user_id");
            //$table->foreign("user_id")->references("id")->on("users");
            $table->string("first_name", 190);
            $table->string("last_name", 190);
            $table->string("email", 150)->nullable();
            $table->string("dni", 150);
            $table->string("document_type", 150);
            $table->string("id_document_file", 150)->nullable();
            $table->string("ss_number", 11)->nullable();
            $table->string("ss_document_name", 11)->nullable();;
            $table->string("ss_document_file", 11)->nullable();;
            $table->string("contract_type")->nullable();
            $table->string("contract_period")->nullable();
            $table->string("contract_reason")->nullable();
            $table->string("contract_weekly_hours")->nullable();
            $table->string("contract_working_hours")->nullable();
            $table->string("contract_schedule")->nullable();
            $table->dateTime("hiring_date")->nullable();
            $table->string("agreement")->nullable()->comment("convenio");
            $table->integer("gross_salary")->nullable();
            $table->integer("net_salary")->nullable();
            $table->integer("number_of_payments")->nullable()->comment("número de pagas");
            $table->string("iban", 100)->nullable();
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
        Schema::dropIfExists('workers');
    }
}
