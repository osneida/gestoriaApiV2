<?php

use App\Http\Controllers\Api\PayrollController;
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
    }


);


/*


    
    Route::group(["prefix" => "certificates", "middleware" => "role:1"], function () {
        Route::post('/upload', 'Api\\CertificateController@upload');
        Route::post('/update', 'Api\\CertificateController@update');
    });


        Route::group(["prefix" => "companies"], function () {
        Route::get('/', 'Api\\CompanyController@index');
    });

    Route::group(["prefix" => "companies"], function () {
        Route::get('/paginated-for-report', 'Api\\CompanyController@paginatedForReport');
        Route::get('/all-filtered', 'Api\\CompanyController@allFiltered');
        Route::get('/{id}', 'Api\\CompanyController@show');
        Route::post('/', 'Api\\CompanyController@store');
        Route::put('/{id}', 'Api\\CompanyController@update');
        Route::put('/{id}/active', 'Api\\CompanyController@activeToogle');
        Route::put('/{id}/workers-acces', 'Api\\CompanyController@workersAccesToogle');
        Route::put('/{id}/sodexo', 'Api\\CompanyController@sodexoToogle');
        Route::put('/{id}/worker-hours', 'Api\\CompanyController@workerHoursToogle');
        Route::put('/{id}/shift-control', 'Api\\CompanyController@shiftControlToogle');
        Route::put('/{id}/holidays', 'Api\\CompanyController@holidaysToogle');
        Route::put('/{id}/complaints', 'Api\\CompanyController@complaintsToogle');
        Route::get('/managers/{id}', 'Api\\CompanyController@getManagers');
        Route::delete('/work-centers/{id}', 'Api\\CompanyController@destroy');
    });

*/
