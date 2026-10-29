<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\BusinessProfile\BusinessProfileController;
use App\Http\Controllers\Client\ClientController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Invoice\InvoiceController;
use App\Http\Controllers\RecurringInvoice\RecurringInvoiceController;
use App\Http\Controllers\Webhook\RazorpayWebhookController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    try {
        DB::select('select 1');
        $db = 'connected';
    } catch (\Throwable $e) {
        $db = 'error: '.$e->getMessage();
    }

    return response()->json([
        'status' => 'ok',
        'time' => now()->toIso8601String(),
        'db' => $db,
    ]);
})->name('ping');

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('AuthController.register')->middleware('throttle:10,1');
    Route::post('/login', [AuthController::class, 'login'])->name('AuthController.login')->middleware('throttle:5,1');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('AuthController.forgotPassword')->middleware('throttle:5,1');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('AuthController.resetPassword')->middleware('throttle:5,1');
});

Route::prefix('auth')->middleware(['jwt.verify'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('AuthController.logout');
});

Route::prefix('clients')->middleware(['jwt.verify'])->group(function () {
    Route::post('/list', [ClientController::class, 'list'])->name('ClientController.list');
    Route::post('/get', [ClientController::class, 'get'])->name('ClientController.get');
    Route::post('/add', [ClientController::class, 'add'])->name('ClientController.add');
    Route::post('/edit', [ClientController::class, 'edit'])->name('ClientController.edit');
    Route::post('/delete', [ClientController::class, 'delete'])->name('ClientController.delete');
});

Route::prefix('invoices')->middleware(['jwt.verify'])->group(function () {
    Route::post('/list', [InvoiceController::class, 'list'])->name('InvoiceController.list');
    Route::post('/get', [InvoiceController::class, 'get'])->name('InvoiceController.get');
    Route::post('/download', [InvoiceController::class, 'download'])->name('InvoiceController.download');
    Route::post('/download-status', [InvoiceController::class, 'downloadStatus'])->name('InvoiceController.downloadStatus');
    Route::post('/download-result', [InvoiceController::class, 'downloadResult'])->name('InvoiceController.downloadResult');
    Route::post('/add', [InvoiceController::class, 'add'])->name('InvoiceController.add');
    Route::post('/clone', [InvoiceController::class, 'clone'])->name('InvoiceController.clone');
    Route::post('/edit', [InvoiceController::class, 'edit'])->name('InvoiceController.edit');
    Route::post('/delete', [InvoiceController::class, 'delete'])->name('InvoiceController.delete');
    Route::post('/update-status', [InvoiceController::class, 'updateStatus'])->name('InvoiceController.updateStatus');
    Route::post('/line-item/add', [InvoiceController::class, 'addLineItem'])->name('InvoiceController.addLineItem');
    Route::post('/line-item/update', [InvoiceController::class, 'updateLineItem'])->name('InvoiceController.updateLineItem');
    Route::post('/line-item/delete', [InvoiceController::class, 'deleteLineItem'])->name('InvoiceController.deleteLineItem');
    Route::post('/create-payment-order', [InvoiceController::class, 'createOrder'])->name('InvoiceController.createOrder');
});

Route::post('/webhooks/razorpay', [RazorpayWebhookController::class, 'handle'])
    ->name('Webhook.razorpay')
    ->middleware('throttle:60,1');

Route::prefix('invoices/public')->middleware('throttle:30,1')->group(function () {
    Route::post('/get', [InvoiceController::class, 'publicGet'])->name('InvoiceController.publicGet');
    Route::post('/create-payment-order', [InvoiceController::class, 'publicCreateOrder'])->name('InvoiceController.publicCreateOrder');
});

Route::prefix('dashboard')->middleware(['jwt.verify'])->group(function () {
    Route::post('/summary', [DashboardController::class, 'summary'])->name('DashboardController.summary');
});

Route::prefix('business-profile')->middleware(['jwt.verify'])->group(function () {
    Route::post('/get', [BusinessProfileController::class, 'get'])->name('BusinessProfileController.get');
    Route::post('/update', [BusinessProfileController::class, 'update'])->name('BusinessProfileController.update');
});

Route::prefix('recurring-invoices')->middleware(['jwt.verify'])->group(function () {
    Route::post('/list', [RecurringInvoiceController::class, 'list'])->name('RecurringInvoiceController.list');
    Route::post('/get', [RecurringInvoiceController::class, 'get'])->name('RecurringInvoiceController.get');
    Route::post('/add', [RecurringInvoiceController::class, 'add'])->name('RecurringInvoiceController.add');
    Route::post('/clone', [RecurringInvoiceController::class, 'clone'])->name('RecurringInvoiceController.clone');
    Route::post('/edit', [RecurringInvoiceController::class, 'edit'])->name('RecurringInvoiceController.edit');
    Route::post('/delete', [RecurringInvoiceController::class, 'delete'])->name('RecurringInvoiceController.delete');
    Route::post('/update-status', [RecurringInvoiceController::class, 'updateStatus'])->name('RecurringInvoiceController.updateStatus');
    Route::post('/line-item/add', [RecurringInvoiceController::class, 'addLineItem'])->name('RecurringInvoiceController.addLineItem');
    Route::post('/line-item/update', [RecurringInvoiceController::class, 'updateLineItem'])->name('RecurringInvoiceController.updateLineItem');
    Route::post('/line-item/delete', [RecurringInvoiceController::class, 'deleteLineItem'])->name('RecurringInvoiceController.deleteLineItem');
});
