<?php

namespace App\Http\Controllers\Api;

use App\Models\Documents;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DocumentsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      
        $search = request()->input('search');
        Log::info('INICIO', ['data' => $search]);
        $user = JWTAuth::parseToken()->authenticate();
        $itemsPerPage = (int) request('itemsPerPage');
        
        if($user->role->id == 3){

            $company_user_id = $user->company->id;

            $gestores_id = $user->company->users()
                            ->wherePivot('role', 'gestor')
                            ->select('users.id')
                            ->get()
                            ->pluck('id');

            $contract = $user->worker->contracts->first();

            $creator = $contract->creator;

            $documents = Documents::where(function($query) use ($creator, $gestores_id) {
                $query->where('creator_id', $creator->id)
                      ->orWhereIn('creator_id', $gestores_id);
              })
              ->where('workers', true)
              ->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 10);

            return response()->json($documents);
                        
            
        }else{
            $documents = Documents::filtered();

            if ($search) {
                $documents->where('name', 'LIKE', '%' . $search . '%');
            }
      
            return response()->json($documents->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 10));

        }
        
    }

    /**
     * Store a newly created resource in storage.
     *
     */
    public function store()
    {
        //
        $data = request()->all();
        $user = JWTAuth::parseToken()->authenticate();
        $data["creator_id"] = $user->id;
        $data["role_editor_id"] = $user->role->id;
        $route = "Documents";
        if (isset($data["company_id"])) {
            $c = Company::find($data["company_id"]);
            $route .= "/$c->slug_name";
        }
        $file = request()->file('file');
        Storage::disk('s3')->putFileAs($route, $file, $file->getClientOriginalName());
        $data["route"] = "$route/" . $file->getClientOriginalName();
        unset($data["file"]);
        $data["workers"] = $data["workers"] === 'true';


        Documents::create($data);
        return ["success" => true];
    }

    /**
     * Display the specified resource.
     *
     */
    public function show($id)
    {
        return Documents::find($id);
    }
    public function generateS3SignedUrl()
    {

        try {
            $url = Storage::disk('s3')->temporaryUrl(
                request('file_route'),
                Carbon::now()->addMinutes(Documents::TIME_SIGNATURE_S3_TEMPORARY_URL)
            );
            return response()->json(["success" => true, "signed_url" => $url]);
        } catch (\Exception $exception) {
            return response()->json(["success" => false, "error" => $exception->getMessage()], $exception->getCode());
        }
    }
    /**
     * Update the specified resource in storage.
     */
    public function update($id)
    {
        //
        $data = request()->all();
        $user = JWTAuth::parseToken()->authenticate();
        $data["creator_id"] = $user->id;
        if ($user->role->id === Role::COMPANY)
            $data["company_id"] = $user->company->id;

        if (request()->hasFile('file')) {

            $file = request()->file('file');
            Storage::disk('s3')->delete($data["route"]);

            $route = "Documents";
            if (isset($data["company_id"])) {
                $c = Company::find($data["company_id"]);
                $route .= "/$c->slug_name";
            }
            Storage::disk('s3')->putFileAs($route, $file, $file->getClientOriginalName());
            $data["route"] = "$route/" . $file->getClientOriginalName();
            unset($data["file"]);
        }
        $data["workers"] = $data["workers"] === 'true';

        Documents::where("id", $id)->update($data);
        return ["success" => true];
    }

    /**
     * Remove the specified resource from storage.
     *
     */
    public function destroy($id)
    {
        //
    }
}
