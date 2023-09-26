<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ContractTotalHours extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('contracts', function (Blueprint $table) {
            $table->double('total_hours', 4, 2)->nullable();
            $table->double('salary_by_hour', 10, 2)->nullable();
           
        });

         DB::statement('ALTER TABLE `contracts` MODIFY `salary` DOUBLE(10,2) NULL;');
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
