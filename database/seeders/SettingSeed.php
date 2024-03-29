<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeed extends Seeder
{

    public function run()
    {
        $seeds = [
            ['key' => 'day_send_payrolls',  "val" => "25"],
            ['key' => 'day_send_iban',      "val" => "25"],
            ['key' => 'day_send_email',     "val" => "25"],
            ['key' => 'day_send_salary',    "val" => "20"],
            ['key' => 'day_send_commissions', "val" => "20"],
            ['key' => 'subject_payrolls',  "val" => "Nòmina"],
            ['key' => 'message_payrolls',  "val" => "Adjuntem la nòmina del mes actual"],

        ];

        Setting::insert($seeds);
        
    }
}




/*
        factory(Setting::class)->create([
            "key" => "day_send_payrolls",
            "val" => "25"
        ]);


        factory(Setting::class)->create([
            "key" => "day_send_iban",
            "val" => "25"
        ]);

        factory(Setting::class)->create([
            "key" => "day_send_email",
            "val" => "25"
        ]);
        factory(Setting::class)->create([
            "key" => "day_send_salary",
            "val" => "20"
        ]);
        factory(Setting::class)->create([
            "key" => "day_send_commissions",
            "val" => "20"
        ]);

        factory(Setting::class)->create([
            "key" => "subject_payrolls",
            "val" => "Nòmina"
        ]);

        factory(Setting::class)->create([
            "key" => "message_payrolls",
            "val" => "Adjuntem la nòmina del mes actual"
        ]);
        */
