<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AppointmentBookingController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DoctorDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PatientDashboardController;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Route;

// Public Clinic & Discovery Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/doctors', [HomeController::class, 'doctors'])->name('doctors.index');
Route::get('/doctors/{doctor}', [HomeController::class, 'doctorDetail'])->name('doctors.show');

// Appointment Booking Engine
Route::get('/book', [AppointmentBookingController::class, 'index'])->name('booking.index');
Route::get('/api/booking/slots', [AppointmentBookingController::class, 'getSlots'])->name('booking.slots');
Route::post('/book', [AppointmentBookingController::class, 'store'])->name('booking.store');
Route::get('/booking/confirmation/{appointment}', [AppointmentBookingController::class, 'confirmation'])->name('booking.confirmation');

// Consultation Fee Payment Checkout & Printable Receipt
Route::get('/booking/checkout/{appointment}', [PaymentController::class, 'checkout'])->name('payment.checkout');
Route::post('/booking/checkout/{appointment}', [PaymentController::class, 'process'])->name('payment.process');
Route::get('/booking/receipt/{appointment}', [PaymentController::class, 'receipt'])->name('payment.receipt');

// Authentication & Quick Demo Login
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quick.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// In-app Notifications
Route::post('/notifications/mark-read', [PatientDashboardController::class, 'markNotificationsRead'])
    ->middleware('auth')
    ->name('notifications.markRead');

// Patient Portal
Route::middleware(['auth', 'role:patient'])->prefix('patient')->name('patient.')->group(function (): void {
    Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/appointments/{appointment}', [PatientDashboardController::class, 'showAppointment'])->name('appointments.show');
    Route::post('/appointments/{appointment}/cancel', [PatientDashboardController::class, 'cancelAppointment'])->name('appointments.cancel');
    Route::get('/medical-records', [PatientDashboardController::class, 'medicalRecords'])->name('records.index');
    Route::get('/medical-records/{record}/prescription', [PatientDashboardController::class, 'showPrescription'])->name('records.prescription');
    Route::get('/medical-records/{record}/pdf', [PatientDashboardController::class, 'downloadPrescriptionPdf'])->name('records.pdf');
    Route::get('/profile', [PatientDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [PatientDashboardController::class, 'updateProfile'])->name('profile.update');
});

// Doctor Portal
Route::middleware(['auth', 'role:doctor'])->prefix('doctor')->name('doctor.')->group(function (): void {
    Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/appointments', [DoctorDashboardController::class, 'appointments'])->name('appointments');
    Route::post('/appointments/{appointment}/status', [DoctorDashboardController::class, 'updateStatus'])->name('appointments.status');
    Route::get('/consultation/{appointment}', [DoctorDashboardController::class, 'showConsultation'])->name('consultation.show');
    Route::post('/consultation/{appointment}', [DoctorDashboardController::class, 'storeConsultation'])->name('consultation.store');
    Route::get('/records/{record}/pdf', [DoctorDashboardController::class, 'downloadPrescriptionPdf'])->name('records.pdf');
    Route::get('/schedule', [DoctorDashboardController::class, 'schedule'])->name('schedule');
    Route::post('/schedule', [DoctorDashboardController::class, 'updateSchedule'])->name('schedule.update');
    Route::post('/leaves', [DoctorDashboardController::class, 'storeLeave'])->name('leaves.store');
    Route::delete('/leaves/{leave}', [DoctorDashboardController::class, 'deleteLeave'])->name('leaves.delete');
    Route::get('/profile', [DoctorDashboardController::class, 'profile'])->name('profile');
    Route::put('/profile', [DoctorDashboardController::class, 'updateProfile'])->name('profile.update');
});

// Clinic Administrator Portal
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/appointments', [AdminDashboardController::class, 'appointments'])->name('appointments.index');
    Route::post('/appointments/{appointment}/status', [AdminDashboardController::class, 'updateAppointmentStatus'])->name('appointments.status');
    Route::post('/appointments/{appointment}/mark-paid', [AdminDashboardController::class, 'markPaymentPaid'])->name('appointments.markPaid');
    Route::get('/doctors', [AdminDashboardController::class, 'doctors'])->name('doctors');
    Route::post('/doctors', [AdminDashboardController::class, 'storeDoctor'])->name('doctors.store');
    Route::put('/doctors/{doctor}', [AdminDashboardController::class, 'updateDoctor'])->name('doctors.update');
    Route::post('/doctors/{doctor}/toggle', [AdminDashboardController::class, 'toggleDoctorAvailability'])->name('doctors.toggle');
    Route::get('/departments', [AdminDashboardController::class, 'departments'])->name('departments');
    Route::post('/departments', [AdminDashboardController::class, 'storeDepartment'])->name('departments.store');
    Route::put('/departments/{department}', [AdminDashboardController::class, 'updateDepartment'])->name('departments.update');
    Route::get('/patients', [AdminDashboardController::class, 'patients'])->name('patients');
});
