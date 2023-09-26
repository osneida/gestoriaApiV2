<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeed extends Seeder
{

    public function run()
    {
        $seeds = [
            ['name' => 'David Godino',           "email" => "david.godino@ggmanagement.cat",    "password" => bcrypt("password"), "role_id" => 1],
            ['name' => 'Ainhoa Madurell Gomez',  "email" => "ainhoa.madurell@ggmanagement.cat", "password" => bcrypt("password"), "role_id" => 1],
        ];

        User::insert($seeds);
    }
}
