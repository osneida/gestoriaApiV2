<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropExtraColumnsWorkersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->dropColumn("id_document_file");
            $table->dropColumn("ss_number");
            $table->dropColumn("ss_document_name");
            $table->dropColumn("ss_document_file");
            $table->dropColumn("contract_type");
            $table->dropColumn("contract_period");
            $table->dropColumn("contract_reason");
            $table->dropColumn("contract_weekly_hours");
            $table->dropColumn("contract_working_hours");
            $table->dropColumn("contract_schedule");
            $table->dropColumn("hiring_date");
            $table->dropColumn("agreement");
            $table->dropColumn("gross_salary");
            $table->dropColumn("net_salary");
            $table->dropColumn("number_of_payments");
            $table->dropColumn("iban");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('workers', function (Blueprint $table) {
            $table->string("id_document_file", 150)->nullable();
            $table->string("ss_number", 11)->nullable();
            $table->string("ss_document_name", 11)->nullable();;
            $table->string("ss_document_file", 11)->nullable();;
            $table->string("contract_type")->nullable();
            $table->string("contract_period")->nullable();
            $table->string("contract_reason")->nullable();
            $table->string("contract_weekly_hours")->nullable();
            $table->string("contract_working_hours")->nullable();
            $table->string("contract_schedule")->nullable();
            $table->dateTime("hiring_date")->nullable();
            $table->string("agreement")->nullable()->comment("convenio");
            $table->integer("gross_salary")->nullable();
            $table->integer("net_salary")->nullable();
            $table->integer("number_of_payments")->nullable()->comment("número de pagas");
            $table->string("iban", 100)->nullable();
        });
    }
}
