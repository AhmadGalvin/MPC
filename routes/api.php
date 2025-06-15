<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ConsultationController;
use App\Http\Controllers\ChatMessageController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ClinicController;
use App\Http\Controllers\Admin\DoctorController as AdminDoctorController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\ChatController;

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

// Public routes
Route::post('/register', [AuthController::class, 'register'])->name('api.auth.register');
Route::post('/login', [AuthController::class, 'login'])->name('api.auth.login');

// Public product routes
Route::get('/products', [ProductController::class, 'index'])->name('api.products.index');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('api.products.show');

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', [AuthController::class, 'me'])->name('api.auth.me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('api.auth.logout');

    // Owner routes
    Route::middleware(['role:owner'])->prefix('owner')->name('api.owner.')->group(function () {
        Route::apiResource('pets', PetController::class);
        Route::post('pets/{pet}/photo', [PetController::class, 'uploadPhoto'])->name('pets.upload-photo');
    });

    // Doctor routes
    Route::middleware(['role:doctor'])->prefix('doctor')->name('api.doctor.')->group(function () {
        Route::apiResource('medical-records', MedicalRecordController::class);
    });

    // Clinic admin routes
    Route::middleware(['role:clinic_admin'])->prefix('clinic')->name('api.clinic.')->group(function () {
        Route::apiResource('doctors', DoctorController::class);
    });

    // Shared routes
    Route::name('api.')->group(function () {
        Route::apiResource('products', ProductController::class)->only(['index', 'show']);
        Route::middleware(['role:clinic_admin'])->group(function () {
            Route::apiResource('products', ProductController::class)->except(['index', 'show']);
        });
    });

    // Consultation routes (Owner & Doctor)
    Route::middleware(['role:owner,doctor'])->name('api.')->group(function () {
        Route::apiResource('consultations', ConsultationController::class);
        Route::post('/consultations/{consultation}/messages', [ConsultationController::class, 'sendMessage'])->name('consultations.messages.store');
        Route::get('/consultations/{consultation}/messages', [ConsultationController::class, 'getMessages'])->name('consultations.messages.index');
    });

    // Chat routes
    Route::get('/consultations/{consultation}/messages', [ChatController::class, 'getMessages']);
    Route::post('/consultations/{consultation}/messages', [ChatController::class, 'sendMessage']);
    Route::post('/consultations/{consultation}/messages/read', [ChatController::class, 'markAsRead']);
});

// Admin Routes
Route::middleware(['auth:sanctum', 'role:clinic_admin'])->prefix('clinic-admin')->name('api.clinic_admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Clinic Profile
    Route::get('/clinic', [AdminController::class, 'getClinicProfile'])->name('clinic.profile');
    Route::put('/clinic', [ClinicController::class, 'update'])->name('clinic.update');
    Route::get('/clinic/statistics', [ClinicController::class, 'statistics'])->name('clinic.statistics');
    
    // Doctor Management
    Route::apiResource('doctors', AdminDoctorController::class);
    
    // Schedule Management
    Route::apiResource('schedules', ScheduleController::class);
    
    // Product Management
    Route::apiResource('products', AdminProductController::class);
    Route::patch('/products/{product}/stock', [AdminProductController::class, 'updateStock'])->name('products.update-stock');
});
