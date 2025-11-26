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
    | Pagamentos - ESTRUTURA CORRIGIDA
    |--------------------------------------------------------------------------
    |
    | 1. Rotas independentes para listagem, visualização, edição e exclusão
    | 2. Rotas aninhadas para criação via fatura específica
    |
    */

    // Rotas independentes de Pagamentos (SEM create/store)
    Route::resource('payments', PaymentController::class)->except(['create', 'store']);

    // Rotas aninhadas de Pagamentos via Faturas
    Route::prefix('invoices/{invoice}')->group(function () {
        // Criar pagamento para fatura específica
        Route::get('/payments/create', [PaymentController::class, 'create'])
            ->name('invoices.payments.create');
        
        // Armazenar pagamento para fatura específica  
        Route::post('/payments', [PaymentController::class, 'store'])
            ->name('invoices.payments.store');
        
        // Pagamento total automático
        Route::post('/payments/full', [PaymentController::class, 'createFullPayment'])
            ->name('invoices.payments.full');
    });

    /*
    |--------------------------------------------------------------------------
    | Relatórios
    |--------------------------------------------------------------------------
    */
    Route::prefix('relatorios')->group(function () {
        Route::get('/financeiro', [ReportController::class, 'financial'])
            ->name('reports.financial');
        Route::get('/estudantes', [ReportController::class, 'students'])
            ->name('reports.students');
        Route::get('/faturas', [ReportController::class, 'invoices'])
            ->name('reports.invoices');
    });
});

// Autenticação Breeze
require __DIR__.'/auth.php';