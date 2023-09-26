<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRenovationInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->integer('renovation_count')->default(0);
            $table->integer('month_renovation_count')->default(0);
            $table->string('baixa_voluntaria_file')->nullable();
            $table->longText('contract_end_motive')->nullable();
            $table->date('contract_end_comunication_date')->nullable();
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
