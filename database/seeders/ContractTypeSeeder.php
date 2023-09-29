<?php

namespace Database\Seeders;

use App\Models\ContractType;
use Illuminate\Database\Seeder;

class ContractTypeSeeder extends Seeder
{

    public function run()
    {
        $seeds = [
            ['name' => 'indefinite'],
            ['name' => 'indefinite_discontinuous'],
            ['name' => 'temporary'],
            ['name' => 'obra_servei'],
            ['name' => 'becari'],
        ];

        ContractType::insert($seeds);
    }
}

/*
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
*/