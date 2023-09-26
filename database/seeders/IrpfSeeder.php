<?php

use App\Models\Setting;
use Illuminate\Database\Seeder;

class IrpfSeeder extends Seeder
{
    /**
     * Run the database seeds
     *
     * @return void
     */
    public function run()
    {
        //
        factory(\App\Models\Irpf::class)->create([
            "min" => 0,
            "cuota" => 0,
            "max" => 12450,
            "percentage" => 19
        ]);

        factory(\App\Models\Irpf::class)->create([
            "min" => 12450,
            "cuota" => 2365.5,
            "max" => 20200,
            "percentage" => 24
        ]);
        factory(\App\Models\Irpf::class)->create([
            "min" => 20200,
            "cuota" => 4225.5,
            "max" => 35200,
            "percentage" => 30
        ]);
        factory(\App\Models\Irpf::class)->create([
            "min" => 35200,
            "cuota" => 8725.5,
            "max" => 60000,
            "percentage" => 37
        ]);
        factory(\App\Models\Irpf::class)->create([
            "min" => 60000,
            "cuota" => 17901.5,
            "max" => 300000,
            "percentage" => 45
        ]);
        factory(\App\Models\Irpf::class)->create([
            "min" => 300000,
            "cuota" => null,
            "max" => null,
            "percentage" => 47
        ]);

        factory(Setting::class)->create([
            "key" => "day_send_commissions",
            "val" => "25"
        ]);
        factory(Setting::class)->create([
            "key" => "day_send_salary",
            "val" => "25"
        ]);
    }
}
