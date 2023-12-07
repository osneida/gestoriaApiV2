<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\WorkCenter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    public function index()
    {
        return response()->json(Company::all());
    }

    public function paginatedForReport()
    {
        $itemsPerPage = (int) request('itemsPerPage');
        $companies = Company::filtered();
        return response()->json(["success" => true, "data" => $companies->paginate($itemsPerPage != 'undefined' ? $itemsPerPage : 10)]);
    }

    public function allFiltered()
    {
        if (request()->wantsJson()) {
            $companies = Company::filtered();
            return response()->json(["success" => true, "data" => $companies->get()]);
        }
        abort(401);
    }

    public function show($id)
    {
        if (request()->wantsJson()) {
            $company = Company::select("id", "name", "cif", "holiday_responsible", "location")
                                ->with(['agreements' => function ($query) {
                                    $query->select('id');
                                }])
                                ->with('workCenters')
                                ->find($id);
            return response()->json(["success" => true, "company" => $company]);
        }
    }

    public function store()
    {
        // Log::info('Datos de entrada: ' . json_encode(request()->all()));
        if (request()->wantsJson()) {
            try {
                DB::beginTransaction();
                $rules = [
                    "name" => 'required|unique:companies|min:2|max:150',
                    "cif" => 'required|unique:companies|max:20'
                ];
                $validator = Validator::make(request()->all(), $rules);

                if ($validator->fails()) {
                    return response()->json(array(
                        'success' => false,
                        'errors' => $validator->getMessageBag()->toArray()
                    ), 422);
                }


                $company = Company::create([
                    "name" => strtoupper(request("name")),
                    "slug" => Str::slug(request("name"), "-"),
                    "cif" => request("cif"),
                    "location" => request("location"),
                    "holiday_responsible" => request("holiday_responsible")
                ]);

                
                $workCenters = request()->input('workCenters');
                Log::info('Datos de entrada: ', ['data' => $workCenters]);
                try {
                    foreach ($workCenters as $workCenter) {
                        $work_center = $company->workCenters()->create([
                            'name' => $workCenter['name']
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error al crear centros de trabajo: ' . $e->getMessage());
                }


                DB::commit();
                return response()->json(["success" => true, "data" => $company]);
            } catch (\Exception $exception) {
                DB::rollBack();
                return response()->json(["success" => false, "data" => $exception->getMessage()]);
            }
        }
    }

    public function update(int $id)
    {
        try {
            DB::beginTransaction();
            $company = Company::find($id);
            if (!$company) {
                return response()->json(["success" => false], 404);
            }
            $rules = [
                "name" => 'required|unique:companies,name,' . $id . '|min:2|max:150',
                "cif" => 'required|unique:companies,cif,' . $id . '|max:20'
            ];
            $validator = Validator::make(request()->all(), $rules);

            if ($validator->fails()) {
                return response()->json(array(
                    'success' => false,
                    'errors' => $validator->getMessageBag()->toArray()
                ), 422);
            }


            if ($company) {
                Log::info('Datos de entrada: ', ['data' => $company]);
                $company->cif = request("cif");
                $company->name = request("name");
                $company->slug = Str::slug(request("name"), "-");
                $company->agreements()->sync(request('agreements'));
                $company->location = request("location");
                $company->holiday_responsible = request("holiday_responsible");
                $workCenters = request()->input('workCenters');
                Log::info('Datos de entrada222222222: ', ['data' => $workCenters]);
                try {
                    foreach ($workCenters as $workCenter) {
                        if (isset($workCenter['id'])) {
                            $company->workCenters()->updateOrCreate(
                                ['id' => $workCenter['id']],
                                ['name' => $workCenter['name']]
                            );
                        } else {
                            $company->workCenters()->create(['name' => $workCenter['name']]);
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Error al crear centros de trabajo: ' . $e->getMessage());
                }
            
                $company->save();
                DB::commit();
            }
            return response()->json(["success" => true]);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(["success" => false, "data" => $exception->getMessage()]);
        }
    }

    public function destroy($id)
    {
        Log::info('Datos de deleteeeee: ', ['data' => $id]);
        $workCenter = WorkCenter::find($id);
        if ($workCenter) {
            $deleted = $workCenter->delete();
            return response()->json(["success" => true, $deleted]);
        } else {
            return response()->json(["success" => false, "message" => "WorkCenter not found"], 404);
        }
    }
    

    public function activeToogle($id)
    {
        $company = Company::where("id", $id);

        return $company->update([
            "active" => !$company->first()->active
        ]);
    }
    public function workersAccesToogle($id)
    {
        $company = Company::where("id", $id);
        $c = $company->first();
        if ($c->workers_access && $c->sodexo) {
            $this->sodexoToogle($id);
        }
        $company->update([
            "workers_access" => !$company->first()->workers_access
        ]);
        return response()->json(["workersAccesToogle" => $company->first()->workers_access]);
    }

    public function sodexoToogle($id)
    {
        $company = Company::where("id", $id);

        $company->update([
            "sodexo" => !$company->first()->sodexo
        ]);
        return response()->json(["sodexo" => $company->first()->sodexo]);
    }

    public function workerHoursToogle($id)
    {
        $company = Company::where("id", $id);

        $company->update([
            "has_worker_hors" => !$company->first()->has_worker_hors
        ]);
        return response()->json(["has_worker_hors" => $company->first()->has_worker_hors]);
    }

    public function shiftControlToogle($id)
    {
        $company = Company::where("id", $id);

        $company->update([
            "has_shift_control" => !$company->first()->has_shift_control
        ]);
        return response()->json(["has_shift_control" => $company->first()->has_shift_control]);
    }

    public function holidaysToogle($id)
    {
        $company = Company::where("id", $id);

        $company->update([
            "holidays" => !$company->first()->holidays
        ]);
        return response()->json(["holidays" => $company->first()->holidays]);
    }

    public function complaintsToogle($id)
    {
        $company = Company::where("id", $id);

        $company->update([
            "complaints_channels" => !$company->first()->complaints_channels
        ]);
        return response()->json(["complaints_channels" => $company->first()->complaints_channels]);
    }

    public function getManagers($id)
    {
        $role = 'sub_gestor';
        $company = Company::where("id", $id)->with(['users' => function ($query) use ($role) {
            $query->wherePivot('role', $role);
        }])->first();
        return $company->users;

    }
    
    public function getPortalWorker(){
        $pw = false;
        $portal = auth()->user()->company()->where('workers_access', 1)->get(); //TODO en la consulta de arriba cambie first por get, osneida
 
        if ($portal){
           $pw = true;
        }
        Log::info('mensaje', ['data' => $pw]);
        return response()->json([
            "portal_worker" => $pw,
        ]);
    }    
}
