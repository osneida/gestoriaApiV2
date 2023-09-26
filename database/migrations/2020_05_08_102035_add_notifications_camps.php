<?php

use Doctrine\DBAL\Schema\Column;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificationsCamps extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dateTime('notificated_at')->nullable();
            $table->integer('notificacion_attemps')->default(0);
            $table->date('finiquito_payed')->nullable();
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->date('not_email')->nullable();
            $table->integer('not_email_attemps')->default(0);
            $table->date('not_iban')->nullable();
            $table->integer('not_iban_attemps')->default(0);
        });

        Schema::table('worker_files', function (Blueprint $table) {
            $table->dateTime('notificated_at')->nullable();
            $table->integer('notificacion_attemps')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
