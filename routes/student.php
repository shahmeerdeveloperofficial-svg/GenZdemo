<?php

use App\Http\Controllers\Admin\Consultation\ReviewController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\ServiceOrderController;
use App\Http\Controllers\Admin\ServiceOrderInvoiceController;
use App\Http\Controllers\Admin\ServiceOrderTaskBoardController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Student\CheckoutController;
use App\Http\Controllers\Student\ConsultationController;
use App\Http\Controllers\Student\DashboardController;
use App\Http\Controllers\Student\EventController;
use App\Http\Controllers\Student\MyCourseController;
use App\Http\Controllers\Student\OnboardingController;
use App\Http\Controllers\Student\StudentProfileController;
use App\Http\Controllers\Student\StudentDocumentController;
use App\Http\Controllers\Student\SavedOptionController;
use App\Http\Controllers\Student\AdmissionApplicationController;
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
Route::get('my-study-profile', [StudentProfileController::class, 'index'])->name('crm.profile');
Route::get('saved-study-options', [SavedOptionController::class, 'index'])->name('saved-options.index');
Route::post('saved-study-options', [SavedOptionController::class, 'store'])->name('saved-options.store');
Route::delete('saved-study-options/{savedOption}', [SavedOptionController::class, 'destroy'])->name('saved-options.destroy');
Route::get('applications', [AdmissionApplicationController::class, 'index'])->name('admissions.index');
Route::get('applications/create', [AdmissionApplicationController::class, 'create'])->name('admissions.create');
Route::post('applications', [AdmissionApplicationController::class, 'store'])->name('admissions.store');
Route::get('applications/{application}', [AdmissionApplicationController::class, 'show'])->name('admissions.show');
Route::post('applications/{application}/documents', [AdmissionApplicationController::class, 'linkDocument'])->name('admissions.documents');
Route::post('applications/{application}/notes', [AdmissionApplicationController::class, 'note'])->name('admissions.notes');
Route::put('my-study-profile', [StudentProfileController::class, 'update'])->name('crm.profile.update');
Route::post('my-study-profile/documents', [StudentDocumentController::class, 'store'])->name('crm.documents.store');
Route::get('my-study-profile/documents/{document}/download', [StudentDocumentController::class, 'download'])->name('crm.documents.download');
Route::get('private-onboarding-files/{file}/download', [StudentDocumentController::class, 'downloadLegacyFile'])->name('service_orders.private_file_download');

Route::group(['prefix' => 'notification', 'as' => 'notification.'], function () {
    Route::get('all-notification', [NotificationController::class, 'allNotification'])->name('all');
    Route::get('view/{id}', [NotificationController::class, 'notificationView'])->name('view');
    Route::get('notification-mark-as-read/{id}', [NotificationController::class, 'notificationMarkAsRead'])->name('notification-mark-as-read');
});

Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::get('/2fa-setting', [ProfileController::class, 'google2faSetting'])->name('2fa');
    Route::post('update', [ProfileController::class, 'update'])->name('update');
    Route::get('password', [ProfileController::class, 'password'])->name('password');
    Route::post('password-update', [ProfileController::class, 'passwordUpdate'])->name('password.update');
});

Route::group(['prefix' => 'event', 'as' => 'event.'], function () {
    Route::get('/', [EventController::class, 'list'])->name('list');
    Route::get('details/{id}', [EventController::class, 'details'])->name('details');
});

Route::group(['prefix' => 'services', 'as' => 'services.'], function () {
    Route::get('/', [ServiceController::class, 'index'])->name('index');
    Route::get('details/{id}', [ServiceController::class, 'details'])->name('details');
});

Route::group(['prefix' => 'consultation-appointment', 'as' => 'consultation-appointment.'], function () {
    Route::get('/', [ConsultationController::class, 'list'])->name('list');
    Route::get('details/{id}', [ConsultationController::class, 'details'])->name('details');
    Route::get('review/{id}', [ConsultationController::class, 'review'])->name('review');
    Route::post('review', [ConsultationController::class, 'reviewStore'])->name('review-store');
    Route::post('{id}/cancel', [ConsultationController::class, 'cancel'])->name('cancel');
    Route::post('{id}/reschedule', [ConsultationController::class, 'reschedule'])->name('reschedule');
});

Route::get('review', [ReviewController::class, 'list'])->name('review');

Route::get('checkout', [CheckoutController::class, 'checkout'])->name('checkout');
Route::post('checkout-pay', [CheckoutController::class, 'pay'])->name('checkout-pay');
Route::get('checkout-verify', [CheckoutController::class, 'verify'])->name('checkout.verify');
Route::get('checkout-response', [CheckoutController::class, 'successOrFail'])->name('checkout.success');

Route::get('transactions', [TransactionController::class, 'index'])->name('transactions');
Route::get('transactions/details/{id}', [TransactionController::class, 'invoiceDetails'])->name('transactions.details');
Route::get('transactions/print/{id}', [TransactionController::class, 'invoicePrint'])->name('transactions.print');

Route::group(['prefix' => 'my-courses', 'as' => 'my_courses.'], function () {
    Route::get('certificates', [\App\Http\Controllers\Student\LmsCertificateController::class, 'index'])->name('certificates.index');
    Route::get('certificates/{certificate}', [\App\Http\Controllers\Student\LmsCertificateController::class, 'show'])->name('certificates.show');
    Route::get('', [MyCourseController::class, 'list'])->name('list');
    Route::get('view/{enrollment_id}', [MyCourseController::class, 'view'])->name('view');
    Route::post('{enrollment_id}/resources/{resource_id}/complete', [MyCourseController::class, 'completeResource'])->name('complete_resource');
    Route::post('{enrollment_id}/resources/{resource_id}/progress', [MyCourseController::class, 'saveProgress'])->name('save_progress');
    Route::get('{enrollment_id}/resources/{resource_id}', [\App\Http\Controllers\Student\CourseResourceController::class, 'show'])->name('resource');
    Route::post('{enrollment_id}/live-sessions/{session_id}/join', [\App\Http\Controllers\Student\CourseLiveSessionController::class, 'join'])->name('live_sessions.join')->middleware('throttle:10,1');
    Route::post('{enrollment_id}/live-sessions/{session_id}/heartbeat', [\App\Http\Controllers\Student\CourseLiveSessionController::class, 'heartbeat'])->name('live_sessions.heartbeat')->middleware('throttle:30,1');
    Route::get('{enrollment_id}/live-sessions/{session_id}/recording', [\App\Http\Controllers\Student\CourseLiveSessionController::class, 'recording'])->name('live_sessions.recording');
    Route::get('{enrollment_id}/quizzes', [\App\Http\Controllers\Student\CourseQuizController::class, 'index'])->name('quizzes.index');
    Route::post('{enrollment_id}/quizzes/{quiz_id}/start', [\App\Http\Controllers\Student\CourseQuizController::class, 'start'])->name('quizzes.start')->middleware('throttle:10,1');
    Route::post('{enrollment_id}/quiz-attempts/{attempt_id}/submit', [\App\Http\Controllers\Student\CourseQuizController::class, 'submit'])->name('quizzes.submit')->middleware('throttle:10,1');
    Route::get('{enrollment_id}/quiz-attempts/{attempt_id}', [\App\Http\Controllers\Student\CourseQuizController::class, 'result'])->name('quizzes.result');
    Route::get('{enrollment_id}/assignments', [\App\Http\Controllers\Student\CourseAssignmentController::class, 'index'])->name('assignments.index');
    Route::post('{enrollment_id}/assignments/{assignment_id}/submissions', [\App\Http\Controllers\Student\CourseAssignmentController::class, 'submit'])->name('assignments.submit');
    Route::get('{enrollment_id}/assignment-submissions/{submission_id}/download', [\App\Http\Controllers\Student\CourseAssignmentController::class, 'download'])->name('assignments.download');
});
Route::get('notification-preferences', [\App\Http\Controllers\Student\NotificationPreferenceController::class, 'index'])->name('notifications.preferences');
Route::post('notification-preferences', [\App\Http\Controllers\Student\NotificationPreferenceController::class, 'update'])->name('notifications.preferences.update');

Route::get('service-orders', [ServiceOrderController::class, 'index'])->name('service_orders');
Route::get('service-orders-documents/{service_order_id}', [ServiceOrderController::class, 'documents'])->name('service_orders.documents');
Route::get('service-orders-onboarding/{service_order_id}', [OnboardingController::class, 'onboarding'])->name('service_orders.onboarding');
Route::get('onboarding/edit-modal/{service_order_id}/{section}/{id?}', [OnboardingController::class, 'editModal'])->name('service_orders.onboarding.edit-modal');
Route::post('onboarding/update/{service_order_id}/{section}/{id?}', [OnboardingController::class, 'update'])->name('service_orders.onboarding.update');
Route::post('onboarding/delete/{service_order_id}/{section}/{id}', [OnboardingController::class, 'delete'])->name('service_orders.onboarding.delete');
Route::post('onboarding/finish/{service_order_id}', [OnboardingController::class, 'onboardFinish'])->name('service_orders.onboarding.finish');

Route::get('service-invoices', [ServiceOrderInvoiceController::class, 'index'])->name('service_invoices.index');
Route::get('get-service-order-{student_id}', [ServiceOrderInvoiceController::class, 'getServiceOrder'])->name('get_service_order');
Route::get('service-invoice-details/{id}', [ServiceOrderInvoiceController::class, 'invoiceDetails'])->name('service_invoices.details');
Route::get('service-invoice-print/{id}', [ServiceOrderInvoiceController::class, 'invoicePrint'])->name('service_invoices.print');

Route::group(['prefix' => 'service-order-task-board', 'as' => 'service_orders.task-board.'], function () {
    Route::get('/{order_id}', [ServiceOrderTaskBoardController::class, 'list'])->name('index');
    Route::get('/{order_id}/view/{id}', [ServiceOrderTaskBoardController::class, 'view'])->name('view');
    Route::group(['prefix' => 'conversation', 'as' => 'conversation.'], function () {
        Route::post('{order_id}/{id}', [ServiceOrderTaskBoardController::class, 'conversationStore'])->name('store');
    });
});
