<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\GuardianController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Rota para notificações

Route::get('/notifications', function () {
    $notifications = auth()->user()->unreadNotifications;
    return view('notifications.index', compact('notifications'));
})->name('notifications.index');

Route::post('/notifications/read', function () {
    auth()->user()->unreadNotifications->markAsRead();
    return back();
});

// Web Routes

Route::get('/', function () {
    return redirect('/dashboard');
});

// Dashboard (todos autenticados com role válida)

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:admin,secretaria,financeiro'])
    ->name('dashboard');

// Rotas protegidas

Route::middleware(['auth'])->group(function () {

    //Perfil do Utilizador (todos)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    //UTILIZADORES (apenas ADMIN)
    Route::prefix('users')->middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs');
        Route::get('/audit-logs/{auditLog}', [AuditLogController::class, 'show'])->name('audit-logs.show');
    });

    //SERVIÇOS (admin + secretaria)
    Route::resource('services', ServiceController::class)
        ->middleware('role:admin,secretaria');

    Route::patch('services/{service}/toggle-status', [ServiceController::class, 'toggleStatus'])
        ->middleware('role:admin,secretaria')
        ->name('services.toggle-status');

    //ESTUDANTES (admin + secretaria)
    Route::resource('students', StudentController::class)
        ->middleware('role:admin,secretaria');

    //ENCARREGADOS DE EDUCAÇÃO (admin + secretaria)
    Route::resource('guardians', GuardianController::class)
        ->middleware('role:admin,secretaria');

    //FATURAS (admin + financeiro)
    Route::resource('invoices', InvoiceController::class)
        ->middleware('role:admin,secretaria');

    Route::post('/invoices/mass-action', [InvoiceController::class, 'handleMassAction'])
        ->name('invoices.mass-action')
        ->middleware('role:admin,financeiro');


    //PAGAMENTOS (admin + financeiro)

    // Rotas principais (sem create/store)
    Route::resource('payments', PaymentController::class)
        ->middleware('role:admin,financeiro')
        ->except(['create', 'store']);

    // Rotas de Confirmação e Rejeição
    Route::patch('/payments/{id}/confirm', [PaymentController::class, 'confirm'])
        ->middleware('role:admin,financeiro')
        ->name('payments.confirm');
    Route::patch('/payments/{id}/reject', [PaymentController::class, 'reject'])
        ->middleware('role:admin,financeiro')
        ->name('payments.reject');

    // Pagamentos vinculados à fatura
    Route::prefix('invoices/{invoice}')
        ->middleware('role:admin,financeiro')
        ->group(function () {

            Route::get('/payments/create', [PaymentController::class, 'create'])
                ->name('invoices.payments.create');

            Route::post('/payments', [PaymentController::class, 'store'])
                ->name('invoices.payments.store');

            Route::post('/payments/full', [PaymentController::class, 'createFullPayment'])
                ->name('invoices.payments.full');
        });


    //RELATÓRIOS (admin + financeiro)
    
    Route::prefix('relatorios')
        ->middleware('role:admin,financeiro')
        ->group(function () {

            Route::get('/financeiro', [ReportController::class, 'financial'])
                ->name('reports.financial');

            Route::get('/estudantes', [ReportController::class, 'students'])
                ->name('reports.students');

            Route::get('/faturas', [ReportController::class, 'invoices'])
                ->name('reports.invoices');

            Route::get('/reports/students/export', [ReportController::class, 'exportStudents'])
                ->name('reports.students.export');

            Route::get('/reports/invoices/export', [ReportController::class, 'exportInvoices'])
                ->name('reports.invoices.export');

            Route::get('/reports/financial/export', [ReportController::class, 'exportFinancial'])
                ->name('reports.financial.export');
        });

});

//Autenticação (Laravel Breeze)
require __DIR__.'/auth.php';