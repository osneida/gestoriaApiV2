<?php

use Database\Seeders\AgreementTableSeeder;
use Database\Seeders\CategoryTableSeeder;
use Database\Seeders\CompanySeed;
use Database\Seeders\ContractTypeSeeder;
use Database\Seeders\IrpfSeeder;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeed;
use Database\Seeders\SettingSeed;
use Database\Seeders\UserSeed;

class DatabaseSeeder extends Seeder
{
  public function run()
  {
    $this->call(RoleSeed::class);
    $this->call(UserSeed::class);
    $this->call(SettingSeed::class);
    $this->call(ContractTypeSeeder::class);

  //  $this->call(AgreementTableSeeder::class);
   // $this->call(CategoryTableSeeder::class);
   // $this->call(CompanySeed::class);
    $this->call(IrpfSeeder::class);


    //    \Illuminate\Support\Facades\Artisan::call('gestoria:import-workers-from-excel');
    //  \Illuminate\Support\Facades\Artisan::call('gestoria:process-salary');
  }
}
