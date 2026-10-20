<?php

use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Affiliate\AffiliateController;
use App\Http\Controllers\Affiliate\WalletController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Affiliate Routes
|--------------------------------------------------------------------------
*/

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

Route::get('dashboard', [AffiliateController::class, 'dashboard'])->name('dashboard');
Route::get('my-affiliate-list', [AffiliateController::class, 'myAffiliations'])->name('my_affiliations');
Route::get('become-affiliate', [AffiliateController::class, 'becomeAffiliate'])->name('become_affiliate');
// Route::get('request-list', [AffiliateController::class, 'requestList'])->name('request_list');
// Route::post('become-affiliate-apply', [AffiliateController::class, 'becomeAffiliateApply'])->name('become-affiliate-apply');

Route::group(['prefix' => 'students', 'as' => 'students.'], function () {
    Route::get('', [AffiliateController::class, 'students'])->name('index');
    Route::post('store', [AffiliateController::class, 'storeStudent'])->name('store');
    Route::get('status-change-modal/{id}', [AffiliateController::class, 'statusChangeModal'])->name('status_change_modal');
    Route::patch('status-change/{id}', [AffiliateController::class, 'statusChange'])->name('status_change');
});

Route::group(['as' => 'wallet.','prefix'=>'wallet'], function () {
    Route::get('/', [WalletController::class, 'index'])->name('index');
    Route::get('transaction-history', [WalletController::class, 'transactionHistory'])->name('transaction-history');
    Route::get('withdrawal-history', [WalletController::class, 'WithdrawalHistory'])->name('withdrawal-history');
    Route::post('process-withdraw', [WalletController::class, 'withdrawProcess'])->name('process-withdraw');
    Route::get('withdrawal-success', [WalletController::class, 'withdrawalSuccess'])->name('withdrawal-success');
    Route::get('beneficiary', [WalletController::class, 'myBeneficiary'])->name('my-beneficiary');
    Route::get('beneficiary-history', [WalletController::class, 'beneficiaryHistory'])->name('beneficiary-history');

    Route::post('save-beneficiary', [WalletController::class, 'saveBeneficiary'])->name('save.my-beneficiary');
    Route::post('status-change-beneficiary/{beneficiary:uuid}', [WalletController::class, 'statusChangeBeneficiary'])->name('beneficiary_status.change');
    Route::get('download-receipt/{uuid}', [WalletController::class, 'downloadReceipt'])->name('download-receipt');
});