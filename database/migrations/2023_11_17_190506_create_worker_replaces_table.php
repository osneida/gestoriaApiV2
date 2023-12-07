<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkerReplacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('worker_replaces', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('worker_id');
            $table->foreign('worker_id')->references('id')->on('workers');

            $table->unsignedBigInteger('worker_id_replace');
            $table->foreign('worker_id_replace')->references('id')->on('workers');

            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('companies');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('worker_replaces');
    }
}
