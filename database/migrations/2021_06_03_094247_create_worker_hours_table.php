<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkerHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('worker_hours', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger("contract_id");
            $table->foreign("contract_id")->references("id")->on("contracts");

            $table->date('date');
            $table->double('hours', 3, 1);



            $table->timestamps();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->boolean("has_worker_hors")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('worker_hours');
    }
}
