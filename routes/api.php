<?php

use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\CommissionController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\DocumentsController;
use App\Http\Controllers\Api\IrpfController;
use App\Http\Controllers\Api\PayrollController;
use App\Http\Controllers\Api\RetainerController;
use App\Http\Controllers\Api\SalaryController;
use App\Http\Controllers\Api\ShiftControlController;
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

        Route::group(["prefix" => "certificates", "middleware" => "role:1,2,3"], function () {
            Route::get('/paginated-for-report', [CertificateController::class, 'index']);
            Route::get('/all-filtered', [CertificateController::class, 'allFiltered']);
            Route::post('/generate-s3-signed-url', [CertificateController::class, 'generateS3SignedUrl']);
            Route::put('/{id}', [CertificateController::class,'markOpened']); //TODO no existe markOpened
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

        Route::group(["prefix" => "shift-control"], function () {
            Route::get('/', [ShiftControlController::class,'index']);
            Route::get('/day', [ShiftControlController::class, 'indexByDay']);
            Route::post('/', [ShiftControlController::class,'store']);
            Route::post('/download', [ShiftControlController::class,'download']);
            Route::post('/download-general', [ShiftControlController::class,'downloadGeneral']);
            Route::get('/{id}', [ShiftControlController::class,'show']);
            Route::post('/{id}', [ShiftControlController::class,'update']);
            Route::delete('/{id}', [ShiftControlController::class,'destroy']); //TODO no tiene funcion
        });

        Route::group(["prefix" => "irpf"], function () {
            Route::get('/', [IrpfController::class, 'index']);
            Route::post('/', [IrpfController::class, 'update']);
        });

        Route::group(["prefix" => "documents", "middleware" => "role:1,2,3"], function () {
            Route::get('/', [DocumentsController::class,'index']);
            Route::post('/', [DocumentsController::class,'store']); //TODO da error The PutObject operation requires non-empty parameter: Bucket
            Route::get('/{id}', [DocumentsController::class,'show']);
            Route::post('/generate-s3-signed-url', [DocumentsController::class,'generateS3SignedUrl']); //TODO error
            Route::post('/{id}', [DocumentsController::class,'update']);
        });
    }


);


/*

 




    Route::group(["prefix" => "settings", "middleware" => "role:1"], function () {
        Route::get('/', 'Api\\SettingController@index');
        Route::put('/', 'Api\\SettingController@update');
    });

    Route::group(["prefix" => "agreements"], function () {
        Route::get('/all', 'Api\\AgreementController@getAll')->middleware(["role:1"]);
        Route::get('/', 'Api\\AgreementController@index');
        Route::post('/', 'Api\\AgreementController@store');
        Route::get('/list', 'Api\\AgreementController@getList');
        Route::get('/{id}', 'Api\\AgreementController@show');
        Route::post('/{id}', 'Api\\AgreementController@update');
        // Route::delete('/{id}', 'Api\\CompanyController@destroy');
    });
    Route::group(["prefix" => "category"], function () {

        Route::get('/{id}', 'Api\\CategoryController@index');
        Route::post('/', 'Api\\CategoryController@store');
        Route::post('/{id}', 'Api\\CategoryController@update');
        Route::delete('/{id}', 'Api\\CategoryController@destroy');
    });
    Route::group(["prefix" => "contracts"], function () {
        Route::group(['middleware' => "role:1,2"], function () {
            Route::get('/{id}/company/{company_id}', 'Api\\ContractController@dataForComplexForm');
            Route::post('/', 'Api\\ContractController@store'); // primer contrato
            Route::put('/{id}', 'Api\\ContractController@update'); // edición
            Route::post('/{id}/new-contract', 'Api\\ContractController@createNewContract'); // posteriores contratos
            Route::post('/{id}/modify', 'Api\\ContractController@modificateLastContract');
            Route::post('/{id}/modify/modification', 'Api\\ContractController@modificationLastContract');
            Route::post('/{id}/baixa', 'Api\\ContractController@baixaContract');
            Route::post('/{id}/finiquito', 'Api\\ContractController@finiquitoPayed');
            Route::post('/manager', 'Api\\ContractController@setManager'); // primer contrato
        });

        Route::get('/mine', 'Api\\ContractController@mycontracts');

        Route::group(['prefix' => 'holidays'], function () {
            Route::get('/', 'Api\\HolidaysController@index');
            Route::get('/agency', 'Api\\HolidaysController@getForAgency');
            Route::get('/days', 'Api\\HolidaysController@getMyCurrentHolidays');
            Route::get('/calendar', 'Api\\HolidaysController@calendar');
            Route::post('/', 'Api\\HolidaysController@store');
            Route::post('/all', 'Api\\HolidaysController@storeAll');
            Route::post('/{id}', 'Api\\HolidaysController@update');
            Route::delete('/{id}', 'Api\\HolidaysController@anulate');
        });
    });

    Route::group(['prefix' => 'notifications'], function () {
        Route::get('/', "Api\\NotificationsController@paginatedForReport");
        Route::get('/count', "Api\\NotificationsController@getCounts");
        Route::get('/filters', "Api\\NotificationsController@filterInfo");
        Route::post('/{id}/read', "Api\\NotificationsController@readNotification");
    });

    Route::group(['prefix' => 'modifications'], function () {
        Route::get('/', "Api\\ModificationController@paginatedForReport");
        Route::get('/filter', "Api\\ModificationController@filterInfo");
    });

    Route::group(["prefix" => "reports"], function () {
        Route::get('/', 'Api\\ReportsController@resume');
        Route::get('/movements-resume', 'Api\\ReportsController@movementsResume');
        Route::get('/movements-resume-anual', 'Api\\ReportsController@movementsResumeAnual');
        Route::get('/payrolls', 'Api\\ReportsController@getPayrollsReport');
        Route::get('/billing', 'Api\\ReportsController@getBillingReport');
        Route::get('/payrolls/resume', 'Api\\ReportsController@payrollsResume');
        Route::get('/payrolls/resume-anual', 'Api\\ReportsController@payrollsResumeAnual');
        Route::get('/active-workers', 'Api\\ReportsController@getActiveWorkers');
        Route::get('/active-workers/monthly', 'Api\\ReportsController@getActiveWorkersMonthly');
        Route::get('/inscriptions', "Api\\ReportsController@registerReportNew");
        Route::get('/updated', "Api\\ReportsController@registerReportUpdated");
        Route::get('/terminated', "Api\\ReportsController@registerReportTerminated");
        Route::get('/holidays', "Api\\ReportsController@getHolidays");
    });

    Route::group(['prefix' => 'complaints'], function () {
        Route::post('/', 'Api\\ComplaintsController@store');
    });
   
    Route::group(["prefix" => "responsible"], function () {
        Route::get('/organigram', 'Api\\AuthController@getOrganigram');
        Route::get('/workers-by-subgestor/{id}', 'Api\\AuthController@getWorkerBySubgestor');
        Route::get('/holiday/workers/{id}', 'Api\\AuthController@getWorkerByResponsible');
    });
    Route::group(["prefix" => "companyrol"], function () {
        Route::get('/rolgestorsubgestor', 'Api\\AuthController@getGestorSubGestor');
    });
    Route::group(["prefix" => "valueportalworker"], function () {
        Route::get('/portalworker', 'Api\\CompanyController@getPortalWorker');
    });
    Route::group(["prefix" => "subgestorname"], function () {
        Route::get('/subgestor-name', 'Api\\WorkerController@getSubGestorName');
    });
    Route::group(["prefix" => "workcenter"], function () {
        Route::get('/work-center', 'Api\\WorkerController@getWorkCenter');
        Route::get('/mine', 'Api\\WorkerController@getMyWorkCenter');
    });
    Route::get('/api-madrid', 'Api\\AuthController@getApiMadrid');
});

*/
