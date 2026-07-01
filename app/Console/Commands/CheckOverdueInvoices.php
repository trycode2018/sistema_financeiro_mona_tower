<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\User;
use App\Notifications\InvoiceReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckOverdueInvoices extends Command
{
    protected $signature = 'invoices:check-overdue';
    protected $description = 'Verifica e notifica faturas que vencem em 3 dias ou estao vencidas';

    public function handle()
    {
        $today = Carbon::today();
        $statusToCheck = ['pendente', 'overdue', 'partial'];
        
        $this->info('=== NOTIFICACOES DE FATURAS ===');
        $this->info('Data: ' . $today->format('d/m/Y'));
        $this->info('Hora: ' . now()->format('H:i:s'));
        $this->info('');
        
        $upcomingCount = 0;
        $overdueCount = 0;
        
        // 1. Faturas que vencem em 3 dias
        $upcomingInvoices = Invoice::whereIn('status', $statusToCheck)
            ->whereDate('due_date', $today->copy()->addDays(3))
            ->get();
        
        foreach ($upcomingInvoices as $invoice) {
            if ($invoice->student && $invoice->student->user) {
                $invoice->student->user->notify(new InvoiceReminder($invoice, 'upcoming_3days'));
                $upcomingCount++;
                $this->info("Notificado: Fatura {$invoice->invoice_number} vence em 3 dias - Aluno: {$invoice->student->name}");
            }
            
            // Notificar administradores
            $admins = User::whereIn('role', ['admin', 'financeiro'])->get();
            foreach ($admins as $admin) {
                $admin->notify(new InvoiceReminder($invoice, 'upcoming_3days'));
            }
        }
        
        // 2. Faturas vencidas
        $overdueInvoices = Invoice::whereIn('status', $statusToCheck)
            ->whereDate('due_date', '<=', $today)
            ->get();
        
        foreach ($overdueInvoices as $invoice) {
            if ($invoice->student && $invoice->student->user) {
                $invoice->student->user->notify(new InvoiceReminder($invoice, 'overdue'));
                $overdueCount++;
                $daysOverdue = Carbon::parse($invoice->due_date)->diffInDays($today);
                $statusText = $daysOverdue == 0 ? 'vence hoje' : "vencida ha {$daysOverdue} dias";
                $this->info("Notificado: Fatura {$invoice->invoice_number} {$statusText} - Aluno: {$invoice->student->name}");
            }
            
            // Notificar administradores
            $admins = User::whereIn('role', ['admin', 'financeiro'])->get();
            foreach ($admins as $admin) {
                $admin->notify(new InvoiceReminder($invoice, 'overdue'));
            }
        }
        
        $this->info('');
        $this->info('=== RESUMO ===');
        $this->info("Faturas que vencem em 3 dias: {$upcomingCount}");
        $this->info("Faturas vencidas: {$overdueCount}");
        $this->info('Total de notificacoes enviadas: ' . ($upcomingCount + $overdueCount));
        $this->info('===============================');
    }
}