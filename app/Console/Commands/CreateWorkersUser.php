<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\AuthController;
use App\Mail\NewUserWorker;
use App\Models\User;
use App\Models\Worker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CreateWorkersUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:workers';

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

        try {
            DB::beginTransaction();
            Log::notice('Vamos a crear usuarios de trabajador');
            $w = Worker
                ::whereDoesntHave("user")
                ->whereHas("companies", function ($q) {
                    $q->where("workers_access", True)->where("active", TRUE);
                })
                ->with(["companies"])
                ->whereNotNull("email")
                ->where("archive", false)
                ->inRandomOrder()
                ->first();
            Log::info('VARIABLE $W', ['data' => $w]);
            if ($w) {
                Log::debug(json_encode($w));
                $u = User::where("email", $w->email)->whereNull("worker_id")->first();
                if ($u) {

                    Log::info('Hay un usuario con este mail vamos a relacionarlos');
                    $u->worker_id = $w->id;
                    $u->company_id = $w->companies[0]->id;
                    $u->save();

                    DB::commit();
                } else {
                    Log::info('No hay usuario vamos a crearlo');
                    $password = AuthController::generateRandomPassword();
                    Log::debug($password);
                    $u = User::create([
                        "name" => "$w->first_name $w->last_name",
                        "email" => $w->email,
                        "company_id" => $w->companies[0]->id,
                        "password" => bcrypt($password),
                        "role_id" => 3,
                        "worker_id" => $w->id

                    ]);
                    //MAIL USUARIO CREADO
                    $email = app()->environment('production') ? $u->email : env("DEVELOPER_MAIL");
                    Mail::to($email)->send(new NewUserWorker($u, $password));
                    if (Mail::failures()) {
                        Log::error('NO SE HA ENVIADO BIEN EL MAIL');
                        DB::rollback();
                    } else {
                        Log::notice("MAIL ENVIADO CORRECTAMENTE");
                        DB::commit();
                    }
                }
            } else {
                Log::notice('No hay usuarios pendientes de crear');
            }
        } catch (\Exception $exception) {
            DB::rollback();
            Log::error("error creando usuario de trabajador: " . $exception->getMessage());
        }
    }
}
