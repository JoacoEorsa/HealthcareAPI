<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Lightit\Appointments\App\Controllers\CancelAppointmentController;
use Lightit\Appointments\App\Controllers\GetAppointmentController;
use Lightit\Appointments\App\Controllers\ListAppointmentController;
use Lightit\Appointments\App\Controllers\ListMyAppointmentController;
use Lightit\Appointments\App\Controllers\StoreAppointmentController;
use Lightit\Appointments\App\Controllers\UpdateAppointmentController;
use Lightit\Authentication\App\Controllers\LoginController;
use Lightit\Authentication\App\Controllers\LogoutController;
use Lightit\Users\App\Controllers\DeleteUserController;
use Lightit\Users\App\Controllers\GetUserController;
use Lightit\Users\App\Controllers\ListUserController;
use Lightit\Users\App\Controllers\StoreUserController;
use Lightit\Users\App\Controllers\UpdateUserController;
use Lightit\Clinics\App\Controllers\DeleteClinicController;
use Lightit\Clinics\App\Controllers\GetClinicController;
use Lightit\Clinics\App\Controllers\ListClinicController;
use Lightit\Clinics\App\Controllers\StoreClinicController;
use Lightit\Clinics\App\Controllers\UpdateClinicController;
use Lightit\Doctors\App\Controllers\DeleteDoctorController;
use Lightit\Doctors\App\Controllers\GetDoctorController;
use Lightit\Doctors\App\Controllers\ListDoctorController;
use Lightit\Doctors\App\Controllers\StoreDoctorController;
use Lightit\Doctors\App\Controllers\UpdateDoctorController;
use Lightit\Doctors\App\Controllers\AssignClinicController;
use Lightit\Patients\App\Controllers\GetMeController;
use Lightit\Patients\App\Controllers\DeletePatientController;
use Lightit\Patients\App\Controllers\GetPatientController;
use Lightit\Patients\App\Controllers\ListPatientController;
use Lightit\Patients\App\Controllers\StorePatientController;
use Lightit\Patients\App\Controllers\UpdatePatientController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')
    ->get('/me', GetMeController::class);

Route::prefix('auth')
    ->group(static function (): void{
        Route::post('/login', LoginController::class);
        Route::middleware('auth:api')
            ->post('/logout', LogoutController::class);
    });

/*
|--------------------------------------------------------------------------
| Users Routes
|--------------------------------------------------------------------------
*/
Route::prefix('users')
    ->group(static function (): void {
        Route::get('/', ListUserController::class);
        Route::post('/', StoreUserController::class);
        Route::prefix('{user}')->group(static function (): void {
            Route::get('/', GetUserController::class)->withTrashed();
            Route::put('/', UpdateUserController::class);
            Route::delete('/', DeleteUserController::class);
        })->whereNumber('user');
});

/*
|--------------------------------------------------------------------------
| Clinics Routes
|--------------------------------------------------------------------------
*/

Route::prefix('clinics')
    ->group(static function (): void {
        Route::get('/', ListClinicController::class);
        Route::post('/', StoreClinicController::class);
        Route::prefix('{clinic}')->group(static function (): void {
            Route::get('/', GetClinicController::class);
            Route::put('/', UpdateClinicController::class);
            Route::delete('/', DeleteClinicController::class);
        })->whereNumber('clinic');
});

/*
|--------------------------------------------------------------------------
| Doctors Routes
|--------------------------------------------------------------------------
*/

Route::prefix('doctors')
    ->group(static function (): void {
        Route::get('/', ListDoctorController::class);
        Route::post('/', StoreDoctorController::class);
        Route::prefix('{doctor}')->group(static function (): void {
            Route::get('/', GetDoctorController::class);
            Route::put('/', UpdateDoctorController::class);
            Route::delete('/', DeleteDoctorController::class);
            Route::post('/clinics', AssignClinicController::class);
        })->whereNumber('doctor');
});

/*
|--------------------------------------------------------------------------
| Patients Routes
|--------------------------------------------------------------------------
*/

Route::prefix('patients')
    ->group(static function (): void {
        Route::get('/', ListPatientController::class);
        Route::post('/', StorePatientController::class);
        Route::prefix('{patient}')->group(static function (): void {
            Route::get('/', GetPatientController::class);
            Route::put('/', UpdatePatientController::class);
            Route::delete('/', DeletePatientController::class);
        })->whereNumber('patient');
    });

Route::prefix('appointments')
    ->group(static function (): void {
        Route::get('/', ListAppointmentController::class);
        Route::middleware('auth:api')->group(static function (): void {
            Route::post('/', StoreAppointmentController::class);
            Route::get('/me', ListMyAppointmentController::class);
            Route::prefix('{appointment}')->group(static function (): void {
                Route::put('/', UpdateAppointmentController::class);
                Route::patch('/', CancelAppointmentController::class);
            })->whereNumber('appointment');
        });
        Route::prefix('{appointment}')->group(static function (): void {
            Route::get('/', GetAppointmentController::class);
        })->whereNumber('appointment');
    });

