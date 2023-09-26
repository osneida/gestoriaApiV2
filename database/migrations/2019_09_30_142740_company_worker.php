<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CompanyWorker extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("company_worker", function (Blueprint $table) {
            $table->unsignedBigInteger("company_id");
            $table->foreign("company_id")->references("id")->on("companies");
            $table->unsignedBigInteger("worker_id");
            $table->foreign("worker_id")->references("id")->on("workers");
            $table->dateTime("inserted_at")->default(now());
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
