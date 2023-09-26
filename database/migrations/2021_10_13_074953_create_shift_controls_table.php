<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShiftControlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shift_controls', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger("contract_id");
            $table->foreign("contract_id")->references("id")->on("contracts");

            $table->date('date');
            $table->string('hour');
            $table->string("action");
            $table->string("description");

            $table->timestamps();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->boolean("has_shift_control")->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shift_controls');
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('has_shift_control');
        });
    }
}
