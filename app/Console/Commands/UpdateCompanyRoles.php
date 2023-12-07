<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateCompanyRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:update-company-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
        $us = User::where("role_id", 2)->whereNotNull('company_id')->get();
        foreach ($us as $u) {
            DB::table('users_companies')->insert([
                "role" => "gestor",
                "user_id" => $u->id,
                "company_id" => $u->company_id
            ]);
        }
    }
}
