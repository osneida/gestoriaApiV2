<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSalaryToContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->double('salary', 10, 2);
            $table->integer('end_motive')->nullable();
            $table->integer('not_enjoyed_vacancies')->nullable();
            $table->unsignedBigInteger('modificated_contract_id')->nullable();
            $table->foreign('modificated_contract_id')->references('id')->on('contracts');
            $table->unsignedBigInteger('creator_id')->default(1);
            $table->foreign('creator_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('contracts', function (Blueprint $table) {
            //
        });
    }
}
