<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ConsultationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ClinicController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\MedicalRecordController;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Doctor\AppointmentController;
use App\Http\Controllers\Doctor\PatientController;
use App\Http\Controllers\Doctor\MedicalRecordController as DoctorMedicalRecordController;
use App\Http\Controllers\Doctor\PrescriptionController;
use App\Http\Controllers\Doctor\ScheduleController as DoctorScheduleController;
use App\Http\Controllers\Owner\AppointmentController as OwnerAppointmentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Owner Routes
    Route::middleware(['auth', 'role:owner'])->name('owner.')->prefix('owner')->group(function () {
        Route::get('/', [App\Http\Controllers\Owner\DashboardController::class, 'index'])->name('dashboard');
        
        // Pet Routes
        Route::resource('pets', App\Http\Controllers\Owner\PetController::class);
        Route::post('pets/{pet}/photo', [App\Http\Controllers\Owner\PetController::class, 'uploadPhoto'])->name('pets.upload-photo');
        
        // Appointment Routes
        Route::resource('appointments', App\Http\Controllers\Owner\AppointmentController::class);
        Route::patch('appointments/{appointment}/cancel', [App\Http\Controllers\Owner\AppointmentController::class, 'cancel'])
            ->name('appointments.cancel');
        
        // Medical Records
        Route::get('medical-records', [MedicalRecordController::class, 'ownerIndex'])->name('medical-records');
        Route::get('medical-records/{record}', [MedicalRecordController::class, 'show'])->name('medical-records.show');
    });

    // Product routes
    Route::resource('products', ProductController::class)->only(['index', 'show']);
    Route::middleware(['role:clinic_admin'])->group(function () {
        Route::resource('products', ProductController::class)->except(['index', 'show']);
    });

    // Doctor Routes
    Route::middleware(['auth', 'role:doctor'])->name('doctor.')->prefix('doctor')->group(function () {
        Route::get('/', [App\Http\Controllers\Doctor\DashboardController::class, 'index'])->name('dashboard');
        
        // Appointment Routes
        Route::resource('appointments', App\Http\Controllers\Doctor\AppointmentController::class);
        Route::patch('appointments/{appointment}/confirm', [App\Http\Controllers\Doctor\AppointmentController::class, 'confirm'])
            ->name('appointments.confirm');
        Route::patch('appointments/{appointment}/complete', [App\Http\Controllers\Doctor\AppointmentController::class, 'complete'])
            ->name('appointments.complete');
        Route::patch('appointments/{appointment}/cancel', [App\Http\Controllers\Doctor\AppointmentController::class, 'cancel'])
            ->name('appointments.cancel');
        
        Route::resource('patients', PatientController::class);
        Route::resource('medical-records', DoctorMedicalRecordController::class);
        Route::resource('prescriptions', PrescriptionController::class);
        Route::get('/schedule', [DoctorScheduleController::class, 'index'])->name('schedule');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'role:clinic_admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/activities', [AdminController::class, 'activities'])->name('activities');

    // Clinic Profile
    Route::get('/clinic/profile', [ClinicController::class, 'profile'])->name('clinic.profile');
    Route::put('/clinic/profile', [ClinicController::class, 'update'])->name('clinic.update');
    Route::get('/clinic/history', [ClinicController::class, 'history'])->name('clinic.history');

    // Doctors Management
    Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
    Route::get('/doctors/{doctor}', [DoctorController::class, 'show'])->name('doctors.show');
    Route::post('/doctors', [DoctorController::class, 'store'])->name('doctors.store');
    Route::put('/doctors/{doctor}', [DoctorController::class, 'update'])->name('doctors.update');
    Route::delete('/doctors/{doctor}', [DoctorController::class, 'destroy'])->name('doctors.destroy');
    Route::post('/doctors/{doctor}/reset-password', [DoctorController::class, 'resetPassword'])->name('doctors.reset-password');

    // Schedule Management
    Route::get('/schedules', [ScheduleController::class, 'index'])->name('schedules.index');
    Route::get('/schedules/events', [ScheduleController::class, 'events'])->name('schedules.events');
    Route::get('/schedules/{schedule}', [ScheduleController::class, 'show'])->name('schedules.show');
    Route::post('/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
    Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
    Route::put('/schedules/{schedule}/update-date', [ScheduleController::class, 'updateDate'])->name('schedules.update-date');
    Route::put('/schedules/{schedule}/update-time', [ScheduleController::class, 'updateTime'])->name('schedules.update-time');
    Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');

    // Medical Records
    Route::get('/medical-records', [MedicalRecordController::class, 'index'])->name('medical-records.index');
    Route::get('/medical-records/{record}', [MedicalRecordController::class, 'show'])->name('medical-records.show');

    // Consultations
    Route::get('/consultations', [ConsultationController::class, 'index'])->name('consultations.index');
    Route::get('/consultations/{consultation}', [ConsultationController::class, 'show'])->name('consultations.show');

    // Products Management
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});

require __DIR__.'/auth.php';
