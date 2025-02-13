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
use App\Http\Controllers\SignatureController;
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

    // Settings Routes
    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [ClubSettingsController::class, 'index'])->name('index');
        Route::patch('/update', [ClubSettingsController::class, 'update'])->name('update');
        
        // Club Settings Routes
        Route::get('/club', [ClubSettingsController::class, 'index'])->name('club');
        Route::post('/club/logo', [ClubSettingsController::class, 'updateLogo'])->name('club.logo');
        Route::delete('/club/logo', [ClubSettingsController::class, 'deleteLogo'])->name('club.logo.delete');
        Route::patch('/club/signature', [ClubSettingsController::class, 'updateDefaultSignature'])->name('club.signature');
        Route::delete('/club/signature', [ClubSettingsController::class, 'deleteDefaultSignature'])->name('club.signature.delete');
        
        // Categories Routes
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
        
        // Signature Management Routes
        Route::resource('signatures', SignatureController::class);
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [ReportController::class, 'index'])->name('index');
        Route::get('/transactions', [ReportController::class, 'transactions'])->name('transactions');
        Route::get('/income', [ReportController::class, 'income'])->name('income');
        Route::get('/expenses', [ReportController::class, 'expenses'])->name('expenses');
        Route::get('/entities', [ReportController::class, 'entities'])->name('entities');
        Route::get('/bank-accounts', [ReportController::class, 'bankAccounts'])->name('bank-accounts');
        Route::get('/category-summary', [ReportController::class, 'categorySummary'])->name('category.summary');
        
        // Individual financial reports
        Route::get('/players/{player}/finances', [ReportController::class, 'playerFinances'])->name('player.finances');
        Route::get('/members/{member}/finances', [ReportController::class, 'memberFinances'])->name('member.finances');
        Route::get('/staff/{staff}/finances', [ReportController::class, 'staffFinances'])->name('staff.finances');
        Route::get('/sponsors/{sponsor}/finances', [ReportController::class, 'sponsorFinances'])->name('sponsor.finances');
        
        // Export routes
        Route::post('/export/pdf', [ReportController::class, 'exportPdf'])->name('export.pdf');
        Route::post('/export/excel', [ReportController::class, 'exportExcel'])->name('export.excel');
        Route::post('/export/csv', [ReportController::class, 'exportCsv'])->name('export.csv');
    });

    Route::patch('/financial-transactions/{transaction}/signature', [FinancialTransactionController::class, 'updateSignature'])
        ->name('financial-transactions.update-signature');

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

    // Attendance Report
    Route::get('attendances/report', [AttendanceController::class, 'report'])
        ->name('attendances.report');
});

require __DIR__.'/auth.php';
