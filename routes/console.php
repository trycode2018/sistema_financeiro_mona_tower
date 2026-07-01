<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Models\Invoice;
use App\Models\User;
use App\Notifications\InvoiceReminder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schedule;

// Comando para verificar faturas diariamente
Schedule::command('invoices:check-alerts')->daily();

Artisan::command('invoices:check-alerts', function () {
    // 1. Alerta para faturas que vencem em 3 dias
    $upcomingInvoices = Invoice::where('status', 'pending')
        ->whereDate('due_date', Carbon::now()->addDays(3))
        ->get();

    // 2. Alerta para faturas que vencem hoje
    $overdueInvoices = Invoice::where('status', 'pending')
        ->whereDate('due_date', Carbon::now())
        ->get();

    $admin = User::where('role', 'admin')->first();

    foreach ($upcomingInvoices as $invoice) {
        $admin->notify(new InvoiceReminder($invoice, 'upcoming'));
    }

    foreach ($overdueInvoices as $invoice) {
        $admin->notify(new InvoiceReminder($invoice, 'overdue'));
    }
})->purpose('Verificar e notificar faturas próximas do vencimento ou vencidas');