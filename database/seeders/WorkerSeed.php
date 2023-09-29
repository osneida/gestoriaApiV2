<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WorkerSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\Worker::class)->create([
            "first_name" => "ELVIRA",
            "last_name" => "DEULOFEU GOMEZ",
            "email" => "elvira@test.com",
            "dni" => "46701258B",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "10794856-83",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 1580,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "123456789"
        ]);

        factory(\App\Models\Worker::class)->create([
            "first_name" => "MONTSERRAT",
            "last_name" => "UBEDA PLA",
            "email" => "montserrat@test.com",
            "dni" => "37292885H",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "04331245-27",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 1880,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "123456789"
        ]);

        /**
         * SOGATSU
         */
        factory(\App\Models\Worker::class)->create([
            "first_name" => "TIAGO",
            "last_name" => "ALMEIDA DE SOUZA",
            "email" => "tuago@shogatsu.com",
            "dni" => "Y7177409C",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "13958012-69",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 1284,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "ES41 2100 0795 1902 0028 5983"
        ]);
        factory(\App\Models\Worker::class)->create([
            "first_name" => "MIGUEL",
            "last_name" => "APARECIDO FONSECA",
            "email" => "miguela@shogatsu.com",
            "dni" => "Y2993057N",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "10527167-54",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 1599,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "ES12 2100 0879 7101 0138 8839"
        ]);
        factory(\App\Models\Worker::class)->create([
            "first_name" => "AGUSTIN TOMAS",
            "last_name" => "BRISABOA",
            "email" => "agus@shogatsu.com",
            "dni" => "Y7377817Y",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "13992018-28",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 1199,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "ES08 0081 0381 9800 0161 4571"
        ]);
        factory(\App\Models\Worker::class)->create([
            "first_name" => "ALICE",
            "last_name" => "BRUNDU",
            "email" => "alice@shogatsu.com",
            "dni" => "Y2433617R",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "12649650-43",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 1279,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "ES41 2100 3049 1621 0194 9901"
        ]);
        factory(\App\Models\Worker::class)->create([
            "first_name" => "GABRIEL SERAFIM",
            "last_name" => "CASASOLLA",
            "email" => "gabriel@shogatsu.com",
            "dni" => "YB2700737",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "13945485-55",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 1284,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "ES66 0081 1694 2400 0123 3634"
        ]);
        factory(\App\Models\Worker::class)->create([
            "first_name" => "RAFAEL",
            "last_name" => "COELHO MARTINEZ",
            "email" => "rafael@shogatsu.com",
            "dni" => "Y6259796S",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "13732036-06",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 1079,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "ES38 2100 0801 1302 0091 8180"
        ]);
        factory(\App\Models\Worker::class)->create([
            "first_name" => "DANIELA",
            "last_name" => "CONDE PAZOS",
            "email" => "daniela@shogatsu.com",
            "dni" => "39429228Y",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "11818748-43",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 1750,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "ES02 2100 0835 1701 0191 4076"
        ]);
        factory(\App\Models\Worker::class)->create([
            "first_name" => "ENZO",
            "last_name" => "DJAMEN FONTCUBERTA",
            "email" => "enzo@shogatsu.com",
            "dni" => "36578633P",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "12103308-05",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 1199,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "ES40 2100 0889 4902 0038 3233"
        ]);
        factory(\App\Models\Worker::class)->create([
            "first_name" => "MARIANNA",
            "last_name" => "DONATI",
            "email" => "marianna@shogatsu.com",
            "dni" => "Y5128888V",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "13427137-75",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 1199,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "ES71 2100 3277 1122 0031 8613"
        ]);
        factory(\App\Models\Worker::class)->create([
            "first_name" => "FERNANDES",
            "last_name" => "LIBARDI ROMAGUERA",
            "email" => "fernandes@shogatsu.com",
            "dni" => "Y7207078L",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "13956785-06",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 1106,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "ES64 2100 0808 1102 0105 1126"
        ]);
        factory(\App\Models\Worker::class)->create([
            "first_name" => "FERNANDA",
            "last_name" => "MARQUES DA PENHA",
            "email" => "fernanda@shogatsu.com",
            "dni" => "Y2618702M",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "12964114-33",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 1408,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "ES86 2100 0656 1701 0108 3822"
        ]);
        factory(\App\Models\Worker::class)->create([
            "first_name" => "MIGUEL",
            "last_name" => "NOGUEIRA MONTEIRO DE BRITO",
            "email" => "miguel@shogatsu.com",
            "dni" => "23876952P",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "12008691-60",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 1929,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "ES58 0182 1004 9702 0000 8432"
        ]);
        factory(\App\Models\Worker::class)->create([
            "first_name" => "PEDRO",
            "last_name" => "OLIVEIRA MACHADO NEVES",
            "email" => "pedro@shogatsu.com",
            "dni" => "Y5999740C",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "13713068-50",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 1099,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "ES30 2100 0958 1502 0020 4604"
        ]);
        factory(\App\Models\Worker::class)->create([
            "first_name" => "MARINA",
            "last_name" => "QUEIROZ FERNANDES",
            "email" => "marina@shogatsu.com",
            "dni" => "Y5085887A",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "13448005-88",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 1216,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "ES95 0081 0226 4900 0140 0250"
        ]);
        factory(\App\Models\Worker::class)->create([
            "first_name" => "BRUNA",
            "last_name" => "RUARO PACHECO",
            "email" => "bruna@shogatsu.com",
            "dni" => "Y7418861H",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "13978441-31",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 445,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "ES90 0081 0057 3500 0245 1353"
        ]);
        factory(\App\Models\Worker::class)->create([
            "first_name" => "LUZIER FELIPE",
            "last_name" => "SANTOS CANALE",
            "email" => "luzier@shogatsu.com",
            "dni" => "Y6168132Y",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "13768148-34",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 1112,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "ES64 0081 4199 2000 0617 4829"
        ]);
        factory(\App\Models\Worker::class)->create([
            "first_name" => "EDUARDO JASON",
            "last_name" => "SCHILLACI",
            "email" => "edu@shogatsu.com",
            "dni" => "Y7405471Z",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "13987098-55",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 1215,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "ES63 2100 3066 2822 0049 6575"
        ]);
        factory(\App\Models\Worker::class)->create([
            "first_name" => "ALEXANDRE",
            "last_name" => "SEIXAS LOPES",
            "email" => "alexandre@shogatsu.com",
            "dni" => "Y5317978R",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "13534433-89",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 1341,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "ES10 0081 0381 9000 0155 2459"
        ]);
        factory(\App\Models\Worker::class)->create([
            "first_name" => "JONATHAN J",
            "last_name" => "TERRANOVA PEREIRO",
            "email" => "jonathan@shogatsu.com",
            "dni" => "Y6899271K",
            "document_type" => "dni",
            "id_document_file" => "test",
            "ss_number" => "13903313-78",
            "hiring_date" => now()->subDays(10),
            "agreement" => "textil",
            "gross_salary" => 915,
            "number_of_payments" => \App\Models\Worker::NUMBER_OF_PAYMENTS[0],
            "iban" => "ES35 2100 3290 4822 0036 2194"
        ]);
    }
}
