<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SyncUserCompanies extends Command
{
    protected $signature = 'sync:user-companies';

    protected $description = 'Syncs users with role_id 2 to user_companies table';

    public function handle()
    {
        $users = User::where('role_id', 2)
            ->whereNotNull('company_id')
            ->get();
    
        $existingUserIds = DB::table('users_companies')
            ->pluck('user_id')
            ->toArray();
    
        foreach ($users as $user) {
            if (!in_array($user->id, $existingUserIds)) {
                DB::table('users_companies')->insert([
                    'user_id' => $user->id,
                    'company_id' => $user->company_id,
                    'role' => 'gestor'
                ]);
            }
        }
    
        $this->info('User companies synchronized successfully.');
    }
    
    
}
