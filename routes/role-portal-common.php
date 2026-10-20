<?php

use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\ProfileController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::get('/2fa-setting', [ProfileController::class, 'google2faSetting'])->name('2fa');
    Route::post('update', [ProfileController::class, 'update'])->name('update');
    Route::get('password', [ProfileController::class, 'password'])->name('password');
    Route::post('password-update', [ProfileController::class, 'passwordUpdate'])->name('password.update');
});

Route::group(['prefix' => 'notification', 'as' => 'notification.'], function () {
    Route::get('all-notification', [NotificationController::class, 'allNotification'])->name('all');
    Route::get('view/{id}', [NotificationController::class, 'notificationView'])->name('view');
    Route::get('notification-mark-as-read/{id}', [NotificationController::class, 'notificationMarkAsRead'])->name('notification-mark-as-read');
});
