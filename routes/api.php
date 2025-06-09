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
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public product routes
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{product}', [ProductController::class, 'show']);

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Owner routes
    Route::middleware(['role:owner'])->group(function () {
        Route::apiResource('pets', PetController::class);
        Route::post('pets/{pet}/photo', [PetController::class, 'uploadPhoto']);
    });

    // Doctor routes
    Route::middleware(['role:doctor'])->group(function () {
        Route::apiResource('medical-records', MedicalRecordController::class);
    });

    // Clinic admin routes
    Route::middleware(['role:clinic_admin'])->group(function () {
        Route::apiResource('doctors', DoctorController::class);
    });

    // Shared routes
    Route::apiResource('products', ProductController::class)->only(['index', 'show']);
    Route::middleware(['role:clinic_admin'])->group(function () {
        Route::apiResource('products', ProductController::class)->except(['index', 'show']);
    });

    // Consultation routes (Owner & Doctor)
    Route::middleware(['role:owner,doctor'])->group(function () {
        Route::apiResource('consultations', ConsultationController::class);
        Route::post('/consultations/{consultation}/messages', [ConsultationController::class, 'sendMessage']);
        Route::get('/consultations/{consultation}/messages', [ConsultationController::class, 'getMessages']);
    });
});

// Admin Routes
Route::middleware(['auth:sanctum', 'role:clinic_admin'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard']);
    
    // Clinic Profile
    Route::get('/clinic', [AdminController::class, 'getClinicProfile']);
    Route::put('/clinic', [ClinicController::class, 'update']);
    Route::get('/clinic/statistics', [ClinicController::class, 'statistics']);
    
    // Doctor Management
    Route::apiResource('doctors', AdminDoctorController::class);
    
    // Schedule Management
    Route::apiResource('schedules', ScheduleController::class);
    
    // Product Management
    Route::apiResource('products', AdminProductController::class);
    Route::patch('/products/{product}/stock', [AdminProductController::class, 'updateStock']);
});
