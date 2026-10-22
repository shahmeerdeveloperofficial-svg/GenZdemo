<?php

use App\Http\Controllers\Admin\Consultation\AppointmentController;
use App\Http\Controllers\Admin\Consultation\ConsulterController;
use App\Http\Controllers\Admin\Consultation\ReviewController;
use App\Http\Controllers\Admin\Consultation\StudentController;
use App\Http\Controllers\Consulter\DashboardController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\AdmissionApplicationController as AdminAdmissionApplicationController;
use App\Http\Controllers\Student\StudentDocumentController;
use App\Http\Controllers\Consulter\AvailabilityController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;

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

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::prefix('crm/leads')->name('crm.leads.')->middleware('can:Manage Leads')->group(function () {
    Route::get('/', [LeadController::class, 'index'])->name('index');
    Route::get('/create', [LeadController::class, 'create'])->name('create');
    Route::post('/', [LeadController::class, 'store'])->name('store');
    Route::get('/imports', [LeadController::class, 'importForm'])->name('imports.create');
    Route::post('/imports/preview', [LeadController::class, 'previewImport'])->name('imports.preview');
    Route::get('/imports/{token}', [LeadController::class, 'showImport'])->name('imports.show');
    Route::post('/imports/{token}/commit', [LeadController::class, 'commitImport'])->name('imports.commit');
    Route::post('/bulk-assign', [LeadController::class, 'bulkAssign'])->name('bulk_assign');
    Route::get('/{lead}/registrations/{registration}/signature', [LeadController::class, 'registrationSignature'])->name('registrations.signature');
    Route::get('/{lead}', [LeadController::class, 'show'])->name('show');
    Route::put('/{lead}', [LeadController::class, 'update'])->name('update');
    Route::delete('/{lead}', [LeadController::class, 'destroy'])->name('destroy');
    Route::post('/{lead}/notes', [LeadController::class, 'addNote'])->name('notes.store');
    Route::post('/{lead}/follow-ups', [LeadController::class, 'addFollowUp'])->name('follow_ups.store');
    Route::post('/follow-ups/{followUp}/complete', [LeadController::class, 'completeFollowUp'])->name('follow_ups.complete');
    Route::post('/follow-ups/{followUp}/comments', [LeadController::class, 'commentOnFollowUp'])->name('follow_ups.comments.store');
});

Route::prefix('admissions')->name('admissions.')->middleware('can:Manage Admissions')->group(function () {
    Route::get('/', [AdminAdmissionApplicationController::class, 'index'])->name('index');
    Route::get('/{application}', [AdminAdmissionApplicationController::class, 'show'])->name('show');
    Route::patch('/{application}/status', [AdminAdmissionApplicationController::class, 'transition'])->name('status');
    Route::patch('/{application}/visa', [AdminAdmissionApplicationController::class, 'visa'])->name('visa');
    Route::post('/{application}/notes', [AdminAdmissionApplicationController::class, 'note'])->name('notes');
    Route::post('/{application}/tasks', [AdminAdmissionApplicationController::class, 'task'])->name('tasks');
    Route::post('/tasks/{task}/complete', [AdminAdmissionApplicationController::class, 'completeTask'])->name('tasks.complete');
});

Route::prefix('student-documents')->name('student_documents.')->middleware('can:Manage Student Documents')->group(function () {
    Route::get('/', [StudentDocumentController::class, 'reviewQueue'])->name('index');
    Route::get('/student/{student}/profile', [StudentDocumentController::class, 'studentProfile'])->name('profile');
    Route::get('/{document}/download', [StudentDocumentController::class, 'download'])->name('download');
    Route::post('/{document}/review', [StudentDocumentController::class, 'review'])->name('review');
    Route::get('/legacy/{file}/download', [StudentDocumentController::class, 'downloadLegacyFile'])->name('legacy_download');
});

Route::prefix('availability/blackouts')->name('availability.blackouts.')->middleware('can:Manage Consultations')->group(function () {
    Route::get('/', [AvailabilityController::class, 'index'])->name('index');
    Route::post('/', [AvailabilityController::class, 'store'])->name('store');
    Route::delete('/{blackout}', [AvailabilityController::class, 'destroy'])->name('destroy');
});

Route::group(['prefix' => 'notification', 'as' => 'notification.'], function () {
    Route::get('all-notification', [NotificationController::class, 'allNotification'])->name('all');
    Route::get('view/{id}', [NotificationController::class, 'notificationView'])->name('view');
    Route::get('notification-mark-as-read/{id}', [NotificationController::class, 'notificationMarkAsRead'])->name('notification-mark-as-read');
});

Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::post('update', [ProfileController::class, 'update'])->name('update');
    Route::get('password', [ProfileController::class, 'password'])->name('password');
    Route::post('password-update', [ProfileController::class, 'passwordUpdate'])->name('password.update');
});

Route::get('consultant-profile', [ConsulterController::class, 'profile'])->name('consultant_profile');
Route::post('consultant-profile', [ConsulterController::class, 'store'])->name('consultant_profile.update');

Route::get('consultant-student', [StudentController::class, 'list'])->name('student.list');

Route::group(['prefix' => 'review', 'as' => 'consultations.review.'], function () {

    Route::get('', [ReviewController::class, 'list'])->name('list');
    Route::get('status-change-modal/{id}', [ReviewController::class, 'statusChangeModal'])->name('status-change-modal');
    Route::post('update-status', [ReviewController::class, 'updateStatus'])->name('update-status');
    Route::post('delete/{id}', [ReviewController::class, 'delete'])->name('delete');

});

Route::group(['prefix' => 'appointments', 'as' => 'consultations.appointments.'], function () {
    Route::get('', [AppointmentController::class, 'index'])->name('index');
    Route::get('view/{id}', [AppointmentController::class, 'view'])->name('view');
    Route::get('status-change-modal/{id}', [AppointmentController::class, 'statusChangeModal'])->name('status_change_modal');
    Route::post('status-change/{id}', [AppointmentController::class, 'statusChange'])->name('status_change');
});

