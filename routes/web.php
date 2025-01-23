<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\PlayerController;
use App\Http\Controllers\SponsorController;
use App\Http\Controllers\FinancialTransactionController;
use App\Http\Controllers\BankAccountController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClubSettingsController;
use App\Http\Controllers\InterBankTransferController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

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
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('users', UserController::class);
    Route::resource('members', MemberController::class);
    Route::resource('staff', StaffController::class);
    Route::resource('players', PlayerController::class);
    Route::resource('sponsors', SponsorController::class);
    Route::resource('bank-accounts', BankAccountController::class);
    Route::resource('financial-transactions', FinancialTransactionController::class)->parameters([
        'financial-transactions' => 'transaction'
    ]);
    Route::get('financial-transactions/{transaction}/download-receipt', [FinancialTransactionController::class, 'downloadReceipt'])
        ->name('financial-transactions.download-receipt');
    Route::get('financial-transactions/{transaction}/download-invoice', [FinancialTransactionController::class, 'downloadInvoice'])
        ->name('financial-transactions.download-invoice');
    Route::resource('interbank-transfers', InterBankTransferController::class);

    // Club Settings Routes
    Route::get('/settings', [ClubSettingsController::class, 'edit'])->name('settings.edit');
    Route::post('/settings/logo', [ClubSettingsController::class, 'updateLogo'])->name('settings.update-logo');
    Route::patch('/settings/features', [ClubSettingsController::class, 'updateFeatures'])->name('settings.update-features');
    Route::get('/settings/club', [ClubSettingsController::class, 'index'])->name('settings.club');
    Route::post('/settings/club/logo', [ClubSettingsController::class, 'updateLogo'])->name('settings.club.logo');
    Route::delete('/settings/club/logo', [ClubSettingsController::class, 'deleteLogo'])->name('settings.club.logo.delete');
    Route::post('/settings/club/features', [ClubSettingsController::class, 'updateFeatures'])->name('settings.club.features');
    Route::get('/settings/categories', [CategoryController::class, 'index'])->name('settings.categories');

    // Attendances
    Route::get('events/attendances', [AttendanceController::class, 'index'])
        ->name('events.attendances.index');
    Route::get('events/{event}/attendances/create', [AttendanceController::class, 'create'])
        ->name('events.attendances.create');
    Route::post('events/{event}/attendances', [AttendanceController::class, 'store'])
        ->name('events.attendances.store');
    Route::get('events/{event}/attendances/edit', [AttendanceController::class, 'edit'])
        ->name('events.attendances.edit');
    Route::put('events/{event}/attendances', [AttendanceController::class, 'update'])
        ->name('events.attendances.update');
    Route::post('attendances/bulk-update', [AttendanceController::class, 'bulkUpdate'])
        ->name('events.attendances.bulk-update');
    Route::get('events/{event}/attendances/report', [AttendanceController::class, 'report'])
        ->name('events.attendances.report');
    Route::get('events/{event}/attendances/export/{format}', [AttendanceController::class, 'export'])
        ->name('events.attendances.export');

    // Events
    Route::resource('events', EventController::class);
    Route::delete('events/{event}/attachments/{attachment}', [EventController::class, 'removeAttachment'])
        ->name('events.remove-attachment');

    Route::resource('categories', CategoryController::class);

    // Financial Reports Routes
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/transactions', [ReportController::class, 'transactions'])->name('transactions');
        Route::get('/income', [ReportController::class, 'income'])->name('income');
        Route::get('/expenses', [ReportController::class, 'expenses'])->name('expenses');
        Route::get('/categories', [ReportController::class, 'categories'])->name('categories');
        Route::get('/entities', [ReportController::class, 'entities'])->name('entities');
        Route::get('/bank-accounts', [ReportController::class, 'bankAccounts'])->name('bank-accounts');
        
        // Export routes
        Route::post('/export/pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');
        Route::post('/export/excel', [ReportController::class, 'exportExcel'])->name('export.excel');
        Route::post('/export/csv', [ReportController::class, 'exportCsv'])->name('export.csv');
    });
});

require __DIR__.'/auth.php';
