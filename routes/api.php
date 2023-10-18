<?php

use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\CommissionController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\PayrollController;
use App\Http\Controllers\Api\RetainerController;
use App\Http\Controllers\Api\SalaryController;
use App\Http\Controllers\Api\WorkerController;
use App\Http\Controllers\Api\WorkerFileController;
use App\Http\Controllers\Api\WorkerHoursController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;

Route::post('authenticate', [AuthController::class, 'login']);
Route::post('forgot_password', [AuthController::class, 'forgotPassword']);
Route::post('reset_password', [AuthController::class, 'resetPassword']);

//Route::post('reset_password', 'Api\\AuthController@resetPassword');

Route::get('phpinfo', function () {
    phpinfo();
});


Route::group(

    ['middleware' => ['auth.jwt']],
    function () {
        Route::group(['prefix' => 'user'], function () {
            Route::post('logout', [AuthController::class, 'logout']);
            Route::post('/',  [AuthController::class, 'store']);
            Route::get('/', [AuthController::class, 'getAuthUser']);
            Route::get('/userinfo', [AuthController::class, 'getUserInfo']);
            Route::post('/password', [AuthController::class, 'changeAuthUserPassword']);
            Route::get('/auth/company/rol', [AuthController::class, 'getCompanyRolAuthUser']);
            Route::get('/paginated-for-report', [AuthController::class, 'getUsers']);
            Route::post('/password/{id}', [AuthController::class, 'resetAuthUserPassword']);
            Route::get('/company/{company_id}', [AuthController::class, 'getCompanyUsers']);
            Route::get('/holiday-responsible', [AuthController::class, 'isHolidayResponsible']);
            // Route::get('/auth/rol', [AuthController::class, 'getRolAuthUser']); //no existe getRolAuthUser
            Route::get('/{id}', [AuthController::class, 'getUserForUptade']);
            Route::post('/{id}', [AuthController::class, 'update']);
            Route::delete('/{id}', [AuthController::class, 'delete']);
            Route::post('/archive/{id}', [AuthController::class, 'delete']);
            Route::post('/switch/{id}', [AuthController::class, 'switchUser']);
        });

        Route::group(['prefix' => 'payrolls', "middleware" => "role:1"], function () {
            Route::post('/upload', [PayrollController::class, 'upload']);
            Route::post('/update', [PayrollController::class, 'update']);
            Route::post('/delete', [PayrollController::class, 'delete']);
        });

        Route::group(["prefix" => "payrolls", "middleware" => "role:1,2,3"], function () {
            Route::get('/paginated-for-report', [PayrollController::class, 'paginatedForReport']);
            Route::get('/all-filtered', [PayrollController::class, 'allFiltered']);
            Route::post('/generate-s3-signed-url', [PayrollController::class, 'generateS3SignedUrl']);
            Route::put('/{id}', [PayrollController::class, 'markOpened']);
            Route::post('/{id}/update', [PayrollController::class, 'updateFile']);
        });


        Route::group(['prefix' => 'certificates', "middleware" => "role:1"], function () {
            Route::post('/upload', [CertificateController::class, 'upload']);
            Route::post('/update', [CertificateController::class, 'update']);
        });

        Route::group(['prefix' => 'companies'], function () {
            Route::get('/',  [CompanyController::class, 'index']);
            Route::get('/paginated-for-report', [CompanyController::class, 'paginatedForReport']);
            Route::get('/all-filtered', [CompanyController::class, 'allFiltered']);
            Route::get('/{id}', [CompanyController::class, 'show']);
            Route::post('/', [CompanyController::class, 'store']);
            Route::put('/{id}', [CompanyController::class, 'update']);
            Route::put('/{id}/active', [CompanyController::class, 'activeToogle']);
            Route::put('/{id}/workers-acces', [CompanyController::class, 'workersAccesToogle']);
            Route::put('/{id}/sodexo', [CompanyController::class, 'sodexoToogle']);
            Route::put('/{id}/worker-hours', [CompanyController::class, 'workerHoursToogle']);
            Route::put('/{id}/shift-control', [CompanyController::class, 'shiftControlToogle']);
            Route::put('/{id}/holidays', [CompanyController::class, 'holidaysToogle']);
            Route::put('/{id}/complaints', [CompanyController::class, 'complaintsToogle']);
            Route::get('managers/{id}',  [CompanyController::class, 'getManagers']);
            Route::delete('/work-centers/{id}',  [CompanyController::class, 'destroy']);
        });

        Route::group(['prefix' => 'workers'], function () {
            Route::get('/paginated-for-report', [WorkerController::class, 'paginatedForReport']);
            Route::get('/all-filtered', [WorkerController::class, 'allFiltered']);
            Route::get('/all-filtered/vacation/{id}', [WorkerController::class, 'allFilteredVacation']);
            Route::get('/all-filtered/working_day', [WorkerController::class, 'allFilteredWorkingDay']);
            Route::get('/filters-data', [WorkerController::class, 'filtersData']);
            Route::get('/{id}/data-for-send-payrolls', [WorkerController::class, 'dataForSendPayrolls']);
            Route::post('/{id}/send-payrolls-by-email', [WorkerController::class, 'sendPayrollsByEmail']);
            Route::post('/{id}/send-certificate-by-email', [WorkerController::class, 'sendCertificateByEmail']);

            Route::post('/edit', [WorkerController::class, 'updateMyWorker']); //TODO  //me da error
            Route::post('/holiday-responsible', [WorkerController::class, 'updateHolidayResponsible']);
            Route::post('/archive/{id}', [WorkerController::class, 'archiveWorker']);

            Route::post('/', [WorkerController::class, 'store']);
            Route::get('/{id}', [WorkerController::class, 'show']);
            Route::post('/{id}', [WorkerController::class, 'update']);
            Route::delete('/{id}/{user_id}', [WorkerController::class, 'destroy']);  //TODO da error


            //ContractController
            Route::post('/manager-responsible', [ContractController::class, 'updateManagerResponsible']);

            //SalaryController
            Route::get('/salary', [SalaryController::class, 'index']);
            Route::get('/salary/all/{id}', [SalaryController::class, 'getAll']);
            Route::post('/salary/add', [SalaryController::class, 'store']);
            Route::post('/salary/edit', [SalaryController::class, 'update']);
            Route::delete('/salary/delete/{id}', [SalaryController::class, 'destroy']);

            //CommissionController
            Route::get('/commission', [CommissionController::class, 'index']);
            Route::post('/commission/add', [CommissionController::class, 'store']);
            Route::post('/commission/edit', [CommissionController::class, 'update']);
            Route::delete('/commission/delete/{id}', [CommissionController::class, 'destroy']);

            //RetainerController
            Route::get('/retainer', [RetainerController::class, 'index']);
            Route::post('/retainer/add', [RetainerController::class, 'store']);
            Route::post('/retainer/edit', [RetainerController::class, 'update']);
            Route::delete('/retainer/delete/{id}', [RetainerController::class, 'destroy']);

            //WorkerFileController  
            Route::get('/{id}/file', [WorkerFileController::class, 'index']);
            Route::get('/{id}/file/{filter}', [WorkerFileController::class, 'getFiltered']);
            Route::post('/{id}/file', [WorkerFileController::class, 'store']);
            Route::post('/{id}/file/generate-s3-signed-url', [WorkerFileController::class, 'generateS3SignedUrl']);
            Route::post('/{id}/file/{file_id}', [WorkerFileController::class, 'update']);
            Route::delete('/{id}/file/{file_id}', [WorkerFileController::class, 'delete']);
            //TODO sin probar 

        });

        Route::group(["prefix" => "worker-hours"], function () {
            Route::get('/', [WorkerHoursController::class,'index']);
            Route::get('/all', [WorkerHoursController::class,'indexAll']);
            Route::get('/total', [WorkerHoursController::class,'sumHours']);
            Route::post('/', [WorkerHoursController::class,'store']);
            Route::get('/{id}', [WorkerHoursController::class,'index']);
            Route::post('/{id}', [WorkerHoursController::class,'update']);
            Route::delete('/{id}', [WorkerHoursController::class,'destroy']);
        });
    }


);


/*

    Route::group(["prefix" => "shift-control"], function () {
        Route::get('/', 'Api\\ShiftControlController@index');
        Route::get('/day', 'Api\\ShiftControlController@indexByDay');
        Route::post('/', 'Api\\ShiftControlController@store');
        Route::post('/download', 'Api\\ShiftControlController@download');
        Route::post('/download-general', 'Api\\ShiftControlController@downloadGeneral');
        Route::get('/{id}', 'Api\\ShiftControlController@show');
        Route::post('/{id}', 'Api\\ShiftControlController@update');
        Route::delete('/{id}', 'Api\\ShiftControlController@destroy');
    });


*/
