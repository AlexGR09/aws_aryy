<?php

use App\Http\Controllers\Sms\Physician\RememberAppointment;
use App\Http\Controllers\TestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(TestController::class)->group(function () {
    Route::post('/test-sms', 'index');
    Route::get('/test-cronjob', 'remember');
    Route::get('/test', 'test');
});

Route::middleware(['auth:sanctum'])->group(function () {
    
    Route::prefix('physician/sms')->group(function () {
        Route::controller(RememberAppointment::class)->group(function () {
            Route::post('/appointment/{appointment_id}', 'rememberAppointmentSms');
        });
    });

});


// Route::group(['middleware' => ['auth:sanctum']], function () {
   
// });




// Route::post('/test-sms', [TestController::class, 'index']);