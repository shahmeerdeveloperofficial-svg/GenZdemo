<?php

use App\Http\Controllers\Portal\DashboardController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/role-portal-common.php';

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
