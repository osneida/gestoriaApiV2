<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NewUserWorker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Models\User;
use App\Models\Company;
use App\Models\Worker;
use App\Models\Contract;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');
        $token = null;
        Log::info('mensaje',['data'=>$credentials]);
        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
            return response()->json(["token" => $token]);
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'error'   => false,
                'message' => 'auth.logged_out'
            ]);
        } catch (TokenExpiredException $exception) {
            return response()->json([
                'error'   => true,
                'message' => 'auth.token.expired'

            ], 401);
        } catch (TokenInvalidException $exception) {
            return response()->json([
                'error'   => true,
                'message' => 'auth.token.invalid'
            ], 401);
        } catch (JWTException $exception) {
            return response()->json([
                'error'   => true,
                'message' => 'auth.token.missing'
            ], 500);
        }
    }

    public function forgotPassword()
    {

        Password::sendResetLink(request()->all());
    }
    public function resetPassword()
    {
        $status = Password::reset(
            request()->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->password = bcrypt($password);
                $user->passwordChanged = true;
                $user->save();
            }
        );
    }

    public function getAuthUser(Request $request)
    {
        $this->validate($request, [
            'token' => 'required'
        ]);

        $user = JWTAuth::authenticate($request->token);

        return response()->json(['user' => $user]);
    }



    public function getCompanyRolAuthUser()
    {
        
        $user = auth()->user()->companies;

        $user->each(function ($company) {
            $company->id = $company->pivot->company_id;
            $company->role = $company->pivot->role;
            unset($company->pivot);
        });

        return response()->json(["success" => true,'user' => $user]);
    }

    public function changeAuthUserPassword(Request $request)
    {

        $credentials = [
            "email" => auth()->user()->email,
            "password" => request("password")
        ];

        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 422);
            }

            if (request("newPassword") != request("newPassword2")) {
                return response()->json(['error' => 'diferent_passwords'], 422);
            }

            User::where("email", $credentials["email"])
                ->update([
                    "password" => bcrypt(request("newPassword")),
                    "passwordChanged" => true
                ]);

            return response()->json(["message" => "password update"]);
        } catch (JWTException $e) {

            // something went wrong whilst attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 422);
        }
    }

    public function resetAuthUserPassword($id, Request $request)
    {
        try {
            $pass = self::generateRandomPassword();
            $u = User::find($id);

            User::where("id", $id)
                ->update([
                    "password" => bcrypt($pass),
                    "passwordChanged" => false
                ]);

            $email = app()->environment('production') ? $u->email : env("DEVELOPER_MAIL");

            Mail::to($u->email)->send(new NewUserWorker($u, $pass));

            return response()->json(["message" => "password update", "success" => true]);
        } catch (\Exception $e) {

            return response()->json(["message" => $e->getMessage(), "success" => false]);
        }
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $rules = [
                "name" => 'required|min:2|max:150',

                "email" => 'required|email',
                "role_id" => 'required',
                "company_id" => ''
            ];




            $validator = Validator::make(request()->all(), $rules);

            if ($validator->fails()) {

                return response()->json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 422);
            }

            $pass = self::generateRandomPassword();

            $u = User::create([
                "name" => request("name"),
                "email" => request("email"),
                "role_id" => request("role_id"),
                "password" => bcrypt($pass),
                //"company_id" => request("company_id")
            ]);

            $c["user_id"] = $u->id;
            $c["role"] = "worker";
            $c["company_id"] = 3;

                DB::table('users_companies')->insert($c);


      //      foreach (request("companies") as $c) {
               // $c["user_id"] = $u->id;
              //  DB::table('users_companies')->insert($c);
          //  }

            $email = app()->environment('production') ? $u->email : env("DEVELOPER_MAIL");

            Mail::to($u->email)->send(new NewUserWorker($u, $pass));

            DB::commit();

            return response()->json(["success" => true]);
        } catch (\Exception $exception) {

            DB::rollback();
            return response()->json(["success" => false, "data" => $exception->getMessage()], 400);
        }
    }



    public function update($id, Request $request)
    {
        //if (request()->wantsJson()) {
        try {
            DB::beginTransaction();
            $rules = [
                "name" => 'required|min:2|max:150',
                "email" => 'required|email',
                "role_id" => 'required',
                "company_id" => ''
            ];


            if (request("role_id") != 1) {
                $rules["company_id"] = 'required';
            }
            $validator = Validator::make(request()->all(), $rules);

            if ($validator->fails()) {
                return response()->json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 422);
            }

            User::where("id", $id)->update([
                "name" => request("name"),
                "email" => request("email"),
                "role_id" => request("role_id"),
                //"password" => bcrypt("password"),
                "company_id" => request("company_id")
            ]);

            DB::table('users_companies')->where('user_id', $id)->delete();

            foreach (request("companies") as $c) {
                $c["user_id"] = $id;
                DB::table('users_companies')->insert($c);
            }

            DB::commit();
            return response()->json(["success" => true]);
        } catch (\Exception $exception) {
            DB::rollback();
            return response()->json(["success" => false, "data" => $exception->getMessage()], 400);
        }
        //}
    }

    public function getUsers(Request $request)
    {
        if (request()->wantsJson()) {
            $itemsPerPage = (int) request('itemsPerPage');
            $users = User::filtered();
            Log::info('mensaje',['data'=>$users]);
            return response()->json(["success" => true, "data" => $users->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 10)]);
        }
    }

    public function getCompanyUsers($company_id)
    {
        return User::where("company_id", $company_id)->get();
    }

    public function getUserForUptade($id, Request $request)
    {
        return User::where("id", $id)->with(['companies' => function ($q) {
            $q->withPivot('role');
        }])->first();
    }

    public function delete($id)
    {
        try {

            $u = User::where("id", $id)->first();
            if ($u->archive) {
                $u->archive = false;
                $u->password = bcrypt("password");
            } else {
                $u->archive = true;
                $u->password = "no_access";
            }
            $u->save();
            return response()->json(["success" => true, "u" => $u]);
        } catch (\Exception $exception) {

            return response()->json(["success" => false, "data" => $exception->getMessage()], 400);
        }
    }

    public static function generateRandomPassword()
    {
        $password = Hash::make(rand(1, 9));
        $password = substr($password, 7, 10);
        return $password;
    }

    public static function isHolidayResponsible()
    {
        $user = JWTAuth::parseToken()->authenticate();
        //Comprovem si te gent al seu carreg
        $w = Worker
            ::where("holiday_responsible", $user->id)
            ->orWhereHas("companies", function ($q) use ($user) {
                $q->where("holiday_responsible", $user->id);
            })->exists();
        if ($w)
            return response()->json(["isHolidayResponsible" => true]);

        //Si no te gent al seu carreg, coprovem en funcio del rol si queda gent sense ningu al seu carreg
        switch ($user->role->id) {
            case 1: //AGENCY
                //return response()->json(["isHolidayResponsible" => true]);
            case 2: //COMPANY
                $we = Worker
                    ::whereNull("holiday_responsible")
                    ->whereHas("companies", function ($q) use ($user) {
                        $q->where("company_id", $user->company_id)->whereNull("holiday_responsible");
                    })->exists();
                if ($we)
                    return response()->json(["isHolidayResponsible" => false]);
                return response()->json(["isHolidayResponsible" => true, 1]);

            case 3: //WORKER
            default:
                return response()->json(["isHolidayResponsible" => false]);
        }
    }

    public static function switchUser($id)
    {
        $targetUser = User::find($id);
        $token = JWTAuth::fromUser($targetUser);
        return response()->json(["token" => $token]);
    }

    
    public function getOrganigram(Request $request)
    {
        #Log::info('INICIO', ['data' => json_encode($request->all())]);
        $user = auth()->user();
        $itemsPerPage = (int) $request->input('itemsPerPage', 10);
        $page = $request->input('page', 1);
        #Log::info('mensaje',['data'=>$user]);
        if ($user->role_id === 1 && $request->input('company') === null) {
            Log::info('mensaje',['data 1 viene por company NULL']);
            $gestoresQuery = User::whereHas('companies', function ($query) {
                $query->where('users_companies.role', 'gestor');
            })->with('company');
            #Log::info('mensaje',['data 2' => $gestoresQuery]);
            // Consulta para subgestores
            $subGestoresQuery = User::whereHas('companies', function ($query) {
                $query->where('users_companies.role', 'sub_gestor');
            })->with('company');
            #Log::info('mensaje',['data 3' => $subGestoresQuery]);
            // Consulta para vacationManager
            $vacationManagerQuery = Worker::whereNotNull('holiday_responsible');
            #Log::info('mensaje',['data 4' => $vacationManagerQuery]);
            // Obtiene los resultados paginados
            $gestores = $gestoresQuery->paginate($itemsPerPage, ['*'], 'gestores_page', $page);
            $subGestores = $subGestoresQuery->paginate($itemsPerPage, ['*'], 'sub_gestores_page', $page);
            $vacationManager = $vacationManagerQuery->paginate($itemsPerPage, ['*'], 'vacation_manager_page', $page);
            #Log::info('mensaje',['data 5' => $gestores]);
            #Log::info('mensaje',['data 6' => $subGestores]);
            #Log::info('mensaje',['data 7' => $vacationManager]);
            // Obtiene los responsables de vacaciones
            $responsibleUsers = User::whereIn('id', $vacationManager->pluck('holiday_responsible')->unique())->with('company')->get();
            #Log::info('mensaje',['data 8' => $responsibleUsers]);

        } elseif ($user->role_id === 1 && $request->input('company') !== null) {
            Log::info('mensaje',['data 1 viene por company TRUE']);
            $gestoresQuery = User::whereHas('companies', function ($query) use ($request) {
                $query->where('users_companies.role', 'gestor')
                    ->where('users_companies.company_id', $request->input('company'));
            })->with('company');
            
            // Filtrar subgestores
            $subGestoresQuery = User::whereHas('companies', function ($query) use ($request) {
                $query->where('users_companies.role', 'sub_gestor')
                    ->where('users_companies.company_id', $request->input('company'));
            })->with('company');
            $companyId = $request->input('company');

            $companies = Company::where('id', $companyId)->get();

            $vacationManager = collect();
            // Filtrar responsables de vacaciones
            foreach ($companies as $company) {
                $vacationManager = $vacationManager->merge($company->workers()->whereNotNull('holiday_responsible')->get());
            }
            
            // Obtiene los resultados paginados
            $gestores = $gestoresQuery->paginate($itemsPerPage, ['*'], 'gestores_page', $page);
            $subGestores = $subGestoresQuery->paginate($itemsPerPage, ['*'], 'sub_gestores_page', $page);
            
            // Obtener los responsables de vacaciones
            $responsibleUsers = User::whereIn('id', $vacationManager->pluck('holiday_responsible')->unique()->toArray())
            ->with('company')
            ->get();
            // Resto del código...
        } else {
            Log::info('mensaje',['paso por el gestor']);
            $companies = $user->companies()->wherePivot('role', 'gestor')->get();
    
            $gestores = collect();
            $subGestores = collect();
            $vacationManager = collect();
    
            foreach ($companies as $company) {
                $gestores = $gestores->merge($company->users()->with('company')->wherePivot('role', 'gestor')->get());
                $subGestores = $subGestores->merge($company->users()->with('company')->wherePivot('role', 'sub_gestor')->get());
                $vacationManager = $vacationManager->merge($company->workers()->whereNotNull('holiday_responsible')->get());
            }
    
            $responsibleUsers = User::whereIn('id', $vacationManager->pluck('holiday_responsible')->unique()->toArray())->with('company')->get();
        }
        return response()->json([
            "gestores" => $gestores->unique('id'),
            "sub_gestores" => $subGestores->unique('id'),
            "vacationManager" => $responsibleUsers,
        ]);
    }

    public function getGestorSubGestor(){
        $isgestor = false;
        $issubgestor = false;
        $companies = auth()->user()->companies()->wherePivot('role', '=', 'gestor')->get();
        $subcompanies = auth()->user()->companies()->wherePivot('role', '=', 'sub_gestor')->get();
    
        if (!$companies->isEmpty()){
            $isgestor = true;
        }
        if (!$subcompanies->isEmpty()){
            $issubgestor = true; 
        }
    
        return response()->json([
            "gestor" => $isgestor,
            "sub_gestor" => $issubgestor
        ]);
    }    

    public function getWorkerBySubgestor($id)
    {
        $user = auth()->user();

        if ($user->role_id == 1 || $user->role_id == 'agencia') {
            Log::info('Mensaje subgestores: Entró en el bloque de subgestores');

            $subgestores = User::whereHas('companies', function ($query) {
                $query->where('users_companies.role', 'sub_gestor');
            })->with('company')->get();

    
            Log::info('Mensaje subgestores: Subgestores encontrados', $subgestores->toArray());
    
            $creatorIds = $subgestores->pluck('id')->toArray();

            $contracts = Contract::whereIn('creator_id', $creatorIds)
                                ->with(['company', 'worker'])
                                ->get();

    
            Log::info('Mensaje subgestores: Contratos encontrados', ['data: ', $contracts]);

            return response()->json([
                "workers" => $contracts,
            ]);
        } else {
            $companies = auth()->user()->companies()->wherePivot('role', '=', 'gestor')->get()->pluck('id');

            $contracts = Contract::whereIn('company_id', $companies)->where('creator_id', $id)->with(['company','worker'])->get();
            Log::info('Mensaje subgestores: Contratos subgestor encontrados', ['data: ', $contracts]);
            return response()->json([
                "workers" => $contracts,
            ]);
        }

    }

    public function getWorkerByResponsible($id){

        $workers = Worker::where('holiday_responsible', $id)->get();

        return response()->json([
            "workers" => $workers,
        ]);
    }
    public function getApiMadrid()
    {
        $url = 'https://datos.comunidad.madrid/catalogo/dataset/2f422c9b-47df-407f-902d-4a2f44dd435e/resource/453162e0-bd61-4f52-8699-7ed5f33168f6/download/festivos_regionales.json';

        $data = file_get_contents($url);

        if ($data !== false) {
            $jsonData = json_decode($data, true);
            return response()->json($jsonData);
        } else {
            return response()->json(['error' => 'Error al obtener los datos'], 500);
        }
    }

    public function getUserInfo()
    {   
        #Log::info('mensaje llego id',['data']);
        $user = auth()->user();
        $user->load('companies'); 
    
        $rolesAndCompanyIds = $user->companies->pluck('pivot.role', 'id')->toArray();
        
        $userInfo = [
            'user' => $user,
            'role' => $rolesAndCompanyIds,
        ];
        #Log::info('mensaje llego id',['data'=> $userInfo]);
        return response()->json($userInfo);
    }
}
