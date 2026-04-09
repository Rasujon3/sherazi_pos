<?php

use App\Http\Controllers\BranchController;
use App\Http\Controllers\DurationController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SpecialityController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\WorkingDayController;
use App\Http\Controllers\WorkingTimeRangeController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\AccessController;
use App\Http\Controllers\DashboardController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [IndexController::class, 'loginPage'])->name('login-page');

Route::get('/admin/login', [IndexController::class, 'loginPage'])->name('login-admin');

Route::post('admin-login', [AccessController::class, 'adminLogin'])->name('admin-login');

Route::get('/logout', [AccessController::class, 'Logout'])->name('logout');

Route::group(['middleware' => ['prevent-back-history', 'admin_auth']], function () {
    // admin dashboard
    Route::get('/dashboard', [DashboardController::class, 'Dashboard'])->name('dashboard');
    Route::get('password-change', [AccessController::class, 'passwordChange'])->name('password-change');
    Route::post('change-password', [AccessController::class, 'changePassword'])->name('change-password');

    // Service Routes
    Route::resource('services', ServiceController::class);
    Route::post('service-status-update', [ServiceController::class, 'serviceStatusUpdate'])->name('service-status-update');

    Route::resource('durations', DurationController::class);
    Route::post('duration-status-update', [DurationController::class, 'durationStatusUpdate'])->name('duration-status-update');

    Route::resource('branches', BranchController::class);
    Route::post('branch-status-update', [BranchController::class, 'branchStatusUpdate'])->name('branch-status-update');

    Route::resource('experiences', ExperienceController::class);
    Route::post('experience-status-update', [ExperienceController::class, 'experienceStatusUpdate'])->name('experience-status-update');

    Route::resource('specialities', SpecialityController::class);
    Route::post('speciality-status-update', [SpecialityController::class, 'specialityStatusUpdate'])->name('speciality-status-update');

    Route::resource('workingdays', WorkingDayController::class);
    Route::post('workingdays-status-update', [WorkingDayController::class, 'workingdaysStatusUpdate'])->name('workingdays-status-update');

    Route::resource('workingtimeranges', WorkingTimeRangeController::class);
    Route::post('workingtimeranges-status-update', [
        WorkingTimeRangeController::class, 'workingtimerangesStatusUpdate'
    ])
        ->name('workingtimeranges-status-update');

    Route::resource('staffs', StaffController::class);
    Route::post('staff-status-update', [
        StaffController::class, 'staffStatusUpdate'
    ])
        ->name('staff-status-update');
    // Add Services
    Route::get('staffs/{staff}/add-services',
        [StaffController::class, 'addServices']
    )->name('staffs.add.services');

    Route::post('staffs/{staff}/store-services',
        [StaffController::class, 'storeServices']
    )->name('staffs.store.services');

    Route::get('staffs/{id}/edit-services', [StaffController::class,'editServices'])
        ->name('staffs.edit.services');

    Route::post('staffs/{staff}/update-services', [StaffController::class,'updateServices'])
        ->name('staffs.update.services');
});


Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize');

    return 'All caches (config, route, view, optimize) have been cleared!';
});

Route::get('/migrate', function(){
    Artisan::call('migrate', [
        '--force' => true,
    ]);
    return response()->json(['message' => 'Migrations run successfully.']);
});

Route::get('/db-seed', function(){
    Artisan::call('db:seed', [
        '--force' => true,
    ]);
    return response()->json(['message' => 'Database seeded successfully.']);
});
