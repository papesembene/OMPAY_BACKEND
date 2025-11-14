<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\QrCodeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Routes publiques - Authentification 2FA uniquement
Route::post('/auth/request-otp', [AuthController::class, 'requestOtp']);
Route::post('/auth/verify-otp', [AuthController::class, 'verifyOtp']);
// Route::post('/auth/login', [AuthController::class, 'login']); // Commenté - Plus utilisé

// Routes protégées
Route::middleware('auth:api')->group(function () {
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Wallets
    Route::get('/wallets', [PaymentController::class, 'checkBalance']);
    Route::get('/wallet/{reference}', [PaymentController::class, 'checkWalletBalance']);
    Route::get('/balance/{reference}', [PaymentController::class, 'checkWalletBalance']); // Alias pour compatibilité

    // Transactions par wallet
    Route::post('/wallet/{reference}/payment', [PaymentController::class, 'makePayment']);
    Route::post('/wallet/{reference}/transfer', [PaymentController::class, 'makeTransfer']);
    Route::get('/wallet/{reference}/qr-payment', [QrCodeController::class, 'generatePaymentQr']);

    // Historique des transactions
    Route::prefix('transactions')->group(function () {
        Route::get('/history', [\App\Http\Controllers\Api\TransactionController::class, 'history']);
        Route::get('/recent', [\App\Http\Controllers\Api\TransactionController::class, 'recent']);
    });
});
