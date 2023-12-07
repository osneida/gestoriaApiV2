<?php

use App\Http\Controllers\Api\AgreementController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\CommissionController;
use App\Http\Controllers\Api\ComplaintsController;
use App\Http\Controllers\Api\ContractController;
use App\Http\Controllers\Api\DocumentsController;
use App\Http\Controllers\Api\HolidaysController;
use App\Http\Controllers\Api\IrpfController;
use App\Http\Controllers\Api\ModificationController;
use App\Http\Controllers\Api\NotificationsController;
use App\Http\Controllers\Api\PayrollController;
use App\Http\Controllers\Api\ReportsController;
use App\Http\Controllers\Api\RetainerController;
use App\Http\Controllers\Api\SalaryController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\ShiftControlController;
use App\Http\Controllers\Api\WorkerController;
use App\Http\Controllers\Api\WorkerDniController;
use App\Http\Controllers\Api\WorkerFileController;
use App\Http\Controllers\Api\WorkerHoursController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CompanyController;

Route::post('authenticate', [AuthController::class, 'login']);
Route::post('forgot_password', [AuthController::class, 'forgotPassword']);
Route::post('reset_password', [AuthController::class, 'resetPassword']);
Route::post('reset_password', [AuthController::class, 'resetPassword']); //TODO 

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
            Route::put('/{id}', [CertificateController::class, 'markOpened']); //TODO no existe markOpened
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

            //company //osneida
            Route::get('/work/company', [WorkerController::class, 'getWorkCompany']);
            Route::get('/work/replace/{id}', [WorkerController::class, 'getWorkerReplaceName']);

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
            Route::get('/', [WorkerHoursController::class, 'index']);
            Route::get('/all', [WorkerHoursController::class, 'indexAll']);
            Route::get('/total', [WorkerHoursController::class, 'sumHours']);
            Route::post('/', [WorkerHoursController::class, 'store']);
            Route::get('/{id}', [WorkerHoursController::class, 'index']);
            Route::post('/{id}', [WorkerHoursController::class, 'update']);
            Route::delete('/{id}', [WorkerHoursController::class, 'destroy']);
        });

        Route::group(["prefix" => "worker-nomina-sin-dni"], function () {
            Route::get('/', [WorkerDniController::class, 'index']);
            Route::get('/all-filtered', [WorkerDniController::class, 'allFiltered']);
            Route::get('/{company_id}', [WorkerDniController::class, 'show']);
            Route::post('/', [WorkerDniController::class, 'store']);
            Route::delete('/{id}', [WorkerDniController::class, 'destroy']);
        });

        Route::group(["prefix" => "shift-control"], function () {
            Route::get('/', [ShiftControlController::class, 'index']);
            Route::get('/day', [ShiftControlController::class, 'indexByDay']);
            Route::post('/', [ShiftControlController::class, 'store']);
            Route::post('/download', [ShiftControlController::class, 'download']);
            Route::post('/download-general', [ShiftControlController::class, 'downloadGeneral']);
            Route::get('/{id}', [ShiftControlController::class, 'show']);
            Route::post('/{id}', [ShiftControlController::class, 'update']);
            Route::delete('/{id}', [ShiftControlController::class, 'destroy']); //TODO no tiene funcion
        });

        Route::group(["prefix" => "irpf"], function () {
            Route::get('/', [IrpfController::class, 'index']);
            Route::post('/', [IrpfController::class, 'update']);
        });

        Route::group(["prefix" => "documents", "middleware" => "role:1,2,3"], function () {
            Route::get('/', [DocumentsController::class, 'index']);
            Route::post('/', [DocumentsController::class, 'store']); //TODO da error The PutObject operation requires non-empty parameter: Bucket
            Route::get('/{id}', [DocumentsController::class, 'show']);
            Route::post('/generate-s3-signed-url', [DocumentsController::class, 'generateS3SignedUrl']); //TODO error
            Route::post('/{id}', [DocumentsController::class, 'update']);
        });

        Route::group(["prefix" => "settings", "middleware" => "role:1"], function () {
            Route::get('/', [SettingController::class, 'index']);
            Route::put('/', [SettingController::class, 'update']);
        });

        Route::group(["prefix" => "agreements"], function () {
            Route::get('/all', [AgreementController::class, 'getAll'])->middleware(["role:1"]);
            Route::get('/', [AgreementController::class, 'index']);
            Route::post('/', [AgreementController::class, 'store']);
            Route::get('/list', [AgreementController::class, 'getList']);
            Route::get('/{id}', [AgreementController::class, 'show']);
            Route::post('/{id}', [AgreementController::class, 'update']);
            Route::get('/list/select', [AgreementController::class, 'getAgreement']);

            // Route::delete('/{id}', 'Api\\CompanyController@destroy');
        });

        Route::group(["prefix" => "category"], function () {

            Route::get('/{id}', [CategoryController::class, 'index']);
            Route::post('/', [CategoryController::class, 'store']);
            Route::post('/{id}', [CategoryController::class, 'update']);
            Route::delete('/{id}', [CategoryController::class, 'destroy']);
        });

        //por revisar

        Route::group(["prefix" => "contracts"], function () {
            Route::group(['middleware' => "role:1,2"], function () {
                Route::get('/{id}/company/{company_id}', [ContractController::class, 'dataForComplexForm']);
                Route::post('/', [ContractController::class, 'store']); // primer contrato
                Route::put('/{id}', [ContractController::class, 'update']); // edición
                Route::post('/{id}/new-contract', [ContractController::class, 'createNewContract']); // posteriores contratos
                Route::post('/{id}/modify', [ContractController::class, 'modificateLastContract']);
                Route::post('/{id}/modify/modification', [ContractController::class, 'modificationLastContract']);
                Route::post('/{id}/baixa', [ContractController::class, 'baixaContract']);
                Route::post('/{id}/finiquito', [ContractController::class, 'finiquitoPayed']);
                Route::post('/manager', [ContractController::class, 'setManager']); // primer contrato
                Route::get('/worker', [ContractController::class, 'workerContracts']); //contratos de un trabajador osneida
            });

            Route::get('/mine', [ContractController::class, 'mycontracts']);

            Route::group(['prefix' => 'holidays'], function () {
                Route::get('/', [HolidaysController::class, 'index']);
                Route::get('/agency', [HolidaysController::class, 'getForAgency']);
                Route::get('/days', [HolidaysController::class, 'getMyCurrentHolidays']);
                Route::get('/calendar', [HolidaysController::class, 'calendar']);
                Route::post('/', [HolidaysController::class, 'store']);
                Route::post('/all', [HolidaysController::class, 'storeAll']);
                Route::post('/{id}', [HolidaysController::class, 'update']);
                Route::delete('/{id}', [HolidaysController::class, 'anulate']);
            });
        });

        Route::group(['prefix' => 'notifications'], function () {
            Route::get('/', [NotificationsController::class, 'paginatedForReport']);
            Route::get('/count', [NotificationsController::class, 'getCounts']);
            Route::get('/filters', [NotificationsController::class, 'filterInfo']);
            Route::post('/{id}/read', [NotificationsController::class, 'readNotification']);
        });

        Route::group(['prefix' => 'modifications'], function () {
            Route::get('/', [ModificationController::class, 'paginatedForReport']);
            Route::get('/filter', [ModificationController::class, 'filterInfo']);
        });

        Route::group(["prefix" => "reports"], function () {
            Route::get('/', [ReportsController::class, 'resume']);
            Route::get('/movements-resume', [ReportsController::class, 'movementsResume']);
            Route::get('/movements-resume-anual', [ReportsController::class, 'movementsResumeAnual']);
            Route::get('/payrolls', [ReportsController::class, 'getPayrollsReport']);
            Route::get('/billing', [ReportsController::class, 'getBillingReport']);
            Route::get('/payrolls/resume', [ReportsController::class, 'payrollsResume']);
            Route::get('/payrolls/resume-anual', [ReportsController::class, 'payrollsResumeAnual']);
            Route::get('/active-workers', [ReportsController::class, 'getActiveWorkers']);
            Route::get('/active-workers/monthly', [ReportsController::class, 'getActiveWorkersMonthly']);
            Route::get('/inscriptions', [ReportsController::class, 'registerReportNew']);
            Route::get('/updated', [ReportsController::class, 'registerReportUpdated']);
            Route::get('/terminated', [ReportsController::class, 'registerReportTerminated']);
            Route::get('/holidays', [ReportsController::class, 'getHolidays']);
        });

        Route::group(['prefix' => 'complaints'], function () {
            Route::post('/', [ComplaintsController::class, 'store']);
        });

        Route::group(["prefix" => "responsible"], function () {
            Route::get('/organigram', [AuthController::class, 'getOrganigram']);
            Route::get('/workers-by-subgestor/{id}', [AuthController::class, 'getWorkerBySubgestor']);
            Route::get('/holiday/workers/{id}', [AuthController::class, 'getWorkerByResponsible']);
        });
        Route::group(["prefix" => "companyrol"], function () {
            Route::get('/rolgestorsubgestor', [AuthController::class, 'getGestorSubGestor']);
        });

        Route::group(["prefix" => "valueportalworker"], function () {
            Route::get('/portalworker', [CompanyController::class, 'getPortalWorker']);
        });
        Route::group(["prefix" => "subgestorname"], function () {
            Route::get('/subgestor-name', [WorkerController::class, 'getSubGestorName']);
        });
        Route::group(["prefix" => "workcenter"], function () {
            Route::get('/work-center', [WorkerController::class, 'getWorkCenter']);
            Route::get('/mine', [WorkerController::class, 'getMyWorkCenter']);
        });
        Route::get('/api-madrid', [AuthController::class, 'getApiMadrid']);
    }


);