<?php

use Illuminate\Database\Seeder;

class ContractTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\ContractType::class)->create([
            "name" => "indefinite"
        ]);
        factory(\App\Models\ContractType::class)->create([
            "name" => "indefinite_discontinuous"
        ]);
        factory(\App\Models\ContractType::class)->create([
            "name" => "temporary"
        ]);
        factory(\App\Models\ContractType::class)->create([
            "name" => "obra_servei"
        ]);

        factory(\App\Models\ContractType::class)->create([
            "name" => "becari"
        ]);
    }
}
