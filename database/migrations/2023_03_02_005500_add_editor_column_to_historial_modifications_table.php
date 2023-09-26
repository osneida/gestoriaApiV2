<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEditorColumnToHistorialModificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('historial_modifications', function (Blueprint $table) {
            $table->unsignedBigInteger('editor_id')->nullable()->after('start_date');
            $table->foreign("editor_id")->references("id")->on("users");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('historial_modifications', function (Blueprint $table) {
         //
        });
    }
}
