<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PetController;
use App\Http\Controllers\ConsultationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\DoctorController;
use App\Http\Controllers\Admin\ScheduleController;
use App\Http\Controllers\Admin\MedicalRecordController;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Doctor\AppointmentController;
use App\Http\Controllers\Doctor\PatientController;
use App\Http\Controllers\Doctor\MedicalRecordController as DoctorMedicalRecordController;
use App\Http\Controllers\Doctor\ScheduleController as DoctorScheduleController;
use App\Http\Controllers\Owner\AppointmentController as OwnerAppointmentController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Admin\PetOwnerController;
use App\Http\Controllers\Admin\PetController as AdminPetController;
use App\Http\Controllers\Admin\AppointmentController as AdminAppointmentController;
use App\Http\Controllers\Admin\FinancialController;
use App\Http\Controllers\Owner\AIChatbotController;

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
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Owner Routes
    Route::middleware(['auth', 'role:owner'])->name('owner.')->prefix('owner')->group(function () {
        Route::get('/', [App\Http\Controllers\Owner\DashboardController::class, 'index'])->name('dashboard');
        
        // Pet Routes
        Route::resource('pets', App\Http\Controllers\Owner\PetController::class);
        Route::post('pets/{pet}/photo', [App\Http\Controllers\Owner\PetController::class, 'uploadPhoto'])->name('pets.upload-photo');
        
        // AI Chatbot Routes
        Route::get('/chatbot', [App\Http\Controllers\Owner\AIChatbotController::class, 'index'])->name('chatbot.index');
        Route::post('/chatbot/chat', [App\Http\Controllers\Owner\AIChatbotController::class, 'chat'])->name('chatbot.chat');
        
        // Appointment Routes
        Route::resource('appointments', App\Http\Controllers\Owner\AppointmentController::class);
        Route::patch('appointments/{appointment}/cancel', [App\Http\Controllers\Owner\AppointmentController::class, 'cancel'])
            ->name('appointments.cancel');
        
        // Consultation Routes
        Route::get('consultations/choose-doctor', [App\Http\Controllers\Owner\ConsultationController::class, 'chooseDoctors'])->name('consultations.choose-doctor');
        Route::get('consultations/create', [App\Http\Controllers\Owner\ConsultationController::class, 'create'])->name('consultations.create');
        Route::post('consultations', [App\Http\Controllers\Owner\ConsultationController::class, 'store'])->name('consultations.store');
        
        // Medical Records
        Route::get('medical-records', [MedicalRecordController::class, 'ownerIndex'])->name('medical-records');
        Route::get('medical-records/{record}', [MedicalRecordController::class, 'show'])->name('medical-records.show');

        // Chat Routes
        Route::get('/consultations/{consultation}/chat', [App\Http\Controllers\Owner\ChatController::class, 'show'])->name('chat.show');
        Route::get('/consultations/{consultation}/messages', [App\Http\Controllers\Owner\ChatController::class, 'getMessages'])->name('chat.messages');
        Route::post('/consultations/{consultation}/messages', [App\Http\Controllers\Owner\ChatController::class, 'store'])->name('chat.store');

        Route::get('/payments/processing', function () {
            return view('owner.payments.processing');
        })->name('payments.processing');
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
        Route::get('/schedule', [DoctorScheduleController::class, 'index'])->name('schedule');
        
        // Consultations
        Route::get('/consultations', [App\Http\Controllers\Doctor\ConsultationController::class, 'index'])->name('consultations.index');
        Route::get('/consultations/{consultation}', [App\Http\Controllers\Doctor\ConsultationController::class, 'show'])->name('consultations.show');
        Route::post('/consultations/toggle-status', [App\Http\Controllers\Doctor\ConsultationController::class, 'toggleStatus'])->name('consultations.toggle-status');
        
        // Chat
        Route::get('/consultations/{consultation}/chat', [App\Http\Controllers\Doctor\ChatController::class, 'show'])->name('chat.show');
        Route::get('/consultations/{consultation}/messages', [App\Http\Controllers\Doctor\ChatController::class, 'getMessages'])->name('chat.messages');
        Route::post('/consultations/{consultation}/messages', [App\Http\Controllers\Doctor\ChatController::class, 'storeMessage'])->name('chat.store');
    });

    // Chat Routes - Consolidated for polymorphic relationships
    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/consultations/{consultation}', [ChatController::class, 'show'])->name('show');
        Route::get('/consultations/{consultation}/messages', [ChatController::class, 'getMessages'])->name('messages.index');
        Route::post('/consultations/{consultation}/messages', [ChatController::class, 'sendMessage'])->name('messages.store');
        Route::post('/consultations/{consultation}/messages/read', [ChatController::class, 'markAsRead'])->name('messages.read');
    });
});

// Admin Routes
Route::middleware(['auth', 'role:clinic_admin'])->group(function () {
    Route::prefix('admin')->name('admin.')->group(function () {
        // Dashboard
        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // Doctors Management
        Route::get('/doctors', [App\Http\Controllers\Admin\DoctorController::class, 'index'])->name('doctors.index');
        Route::get('/doctors/create', [App\Http\Controllers\Admin\DoctorController::class, 'create'])->name('doctors.create');
        Route::post('/doctors/create-step-one', [App\Http\Controllers\Admin\DoctorController::class, 'storeStepOne'])->name('doctors.store.step.one');
        Route::get('/doctors/create-step-two/{user}', [App\Http\Controllers\Admin\DoctorController::class, 'createStepTwo'])->name('doctors.create.step.two');
        Route::post('/doctors/{user}', [App\Http\Controllers\Admin\DoctorController::class, 'store'])->name('doctors.store');
        Route::get('/doctors/{doctor}', [App\Http\Controllers\Admin\DoctorController::class, 'show'])->name('doctors.show');
        Route::get('/doctors/{doctor}/edit', [App\Http\Controllers\Admin\DoctorController::class, 'edit'])->name('doctors.edit');
        Route::put('/doctors/{doctor}', [App\Http\Controllers\Admin\DoctorController::class, 'update'])->name('doctors.update');
        Route::delete('/doctors/{doctor}', [App\Http\Controllers\Admin\DoctorController::class, 'destroy'])->name('doctors.destroy');

        // Pet Owners Management
        Route::get('/owners', [PetOwnerController::class, 'index'])->name('owners.index');
        Route::get('/owners/create-step-one', [PetOwnerController::class, 'createStepOne'])->name('owners.create-step-one');
        Route::post('/owners/store-step-one', [PetOwnerController::class, 'storeStepOne'])->name('owners.store-step-one');
        Route::get('/owners/{owner}/create-step-two', [PetOwnerController::class, 'createStepTwo'])->name('owners.create-step-two');
        Route::post('/owners/{owner}', [PetOwnerController::class, 'store'])->name('owners.store');
        Route::get('/owners/{owner}', [PetOwnerController::class, 'show'])->name('owners.show');
        Route::get('/owners/{owner}/edit', [PetOwnerController::class, 'edit'])->name('owners.edit');
        Route::put('/owners/{owner}', [PetOwnerController::class, 'update'])->name('owners.update');
        Route::delete('/owners/{owner}', [PetOwnerController::class, 'destroy'])->name('owners.destroy');

        // Pets
        Route::resource('pets', AdminPetController::class);
        
        // Medical Records Management
        Route::resource('medical-records', MedicalRecordController::class);
        
        // Appointments Management
        Route::resource('appointments', AdminAppointmentController::class);
        Route::patch('/appointments/{appointment}/confirm', [AdminAppointmentController::class, 'confirm'])->name('appointments.confirm');
        Route::patch('/appointments/{appointment}/complete', [AdminAppointmentController::class, 'complete'])->name('appointments.complete');
        Route::patch('/appointments/{appointment}/cancel', [AdminAppointmentController::class, 'cancel'])->name('appointments.cancel');

        // Financial Management
        Route::get('/financial', [FinancialController::class, 'index'])->name('financial.index');
    });
});

// Payment Routes
Route::prefix('payments')->name('payments.')->group(function () {
    Route::get('process/{consultation}', [PaymentController::class, 'process'])->name('process');
    Route::post('callback', [PaymentController::class, 'callback'])->name('callback');
    Route::get('success/{consultation}', [PaymentController::class, 'success'])->name('success');
    Route::get('failed/{consultation}', [PaymentController::class, 'failed'])->name('failed');
});

require __DIR__.'/auth.php';
