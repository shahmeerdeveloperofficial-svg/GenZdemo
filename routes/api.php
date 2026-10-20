<?php

use App\Http\Controllers\Student\CheckoutController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//call back
Route::match(array('GET', 'POST'), 'verify', [CheckoutController::class, 'verify'])->name('payment.verify');

// Affiliate routes
Route::get('check-affiliate-key', [App\Http\Controllers\Affiliate\AffiliateController::class, 'checkAffiliatekey'])->name('check.affiliate.key');
Route::post('apply-affiliate-commission', [App\Http\Controllers\Affiliate\AffiliateController::class, 'applyAffiliateCommission'])->name('apply.affiliate.commission');
