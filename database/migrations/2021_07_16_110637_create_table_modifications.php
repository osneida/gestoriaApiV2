<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTableModifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('historial_modifications', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger("contract_id");
            $table->foreign("contract_id")->references("id")->on("contracts");
            $table->string("type");
            $table->string("motive")->nullable();
            $table->date("start_date");
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
        Schema::dropIfExists('historial_modifications');
    }
}
