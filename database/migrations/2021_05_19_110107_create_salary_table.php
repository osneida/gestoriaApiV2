<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("contract_id");
            $table->foreign("contract_id")->references("id")->on("contracts");
            $table->integer("salary");
            $table->date("start_date");
            $table->date("end_date")->nullable();
            $table->date("notificated_at")->nullable();
            $table->integer("notificated_attempts")->nullable();
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
        Schema::dropIfExists('salary');
    }
}
