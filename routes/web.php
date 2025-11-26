<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect('/dashboard');
});

// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Perfil do Utilizador
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    /*
    |--------------------------------------------------------------------------
    | Estudantes
    |--------------------------------------------------------------------------
    */
    Route::resource('students', StudentController::class);

    /*
    |--------------------------------------------------------------------------
    | Utilizadores
    |--------------------------------------------------------------------------
    */
    Route::resource('users', UserController::class);
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])
        ->name('users.toggle-status');

    /*
    |--------------------------------------------------------------------------
    | Faturas
    |--------------------------------------------------------------------------
    */
    Route::resource('invoices', InvoiceController::class);

    /*
    |--------------------------------------------------------------------------
    | Pagamentos (ACESSO DUPLO)
    |--------------------------------------------------------------------------
    |
    | 1. Acesso independente:
    |       /payments
    |       /payments/{payment}
    |
    | 2. Acesso por fatura:
    |       /invoices/{invoice}/payments/create
    |       /invoices/{invoice}/payments
    |
    |--------------------------------------------------------------------------
    */

    // Acesso independente
    Route::resource('payments', PaymentController::class);

    // Acesso via fatura
    Route::get('invoices/{invoice}/payments/create', [PaymentController::class, 'create'])
        ->name('invoices.payments.create');

    Route::post('invoices/{invoice}/payments', [PaymentController::class, 'store'])
        ->name('invoices.payments.store');

    /*
    |--------------------------------------------------------------------------
    | Relatórios
    |--------------------------------------------------------------------------
    */
    Route::get('/relatorios/financeiro', [ReportController::class, 'financial'])
        ->name('reports.financial');

    Route::get('/relatorios/estudantes', [ReportController::class, 'students'])
        ->name('reports.students');
});

// Autenticação Breeze
require __DIR__.'/auth.php';
