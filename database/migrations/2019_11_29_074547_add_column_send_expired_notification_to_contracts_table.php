<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnSendExpiredNotificationToContractsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dateTime("send_expired_notification")->nullable()->comment("Flag para saber si se ha enviado la notificación a la empresa de que el contrato está próximo a expirar");
            $table->string("reason_not_send_expired_notification")->nullable()->comment("El motivo por si algo falla al intentar notificar la expiración");
            $table->tinyInteger("attempts_send_expired_notification")->default(0)->comment("Número de intentos para notificar la expiración del contrato a la empresa");
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
            $table->dropColumn("send_expired_notification");
            $table->dropColumn("reason_not_send_expired_notification");
            $table->dropColumn("attempts_send_expired_notification");
        });
    }
}
