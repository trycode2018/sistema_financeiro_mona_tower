<?php

namespace App\Traits;

use App\Models\Invoice;
use App\Notifications\InvoiceReminder;
use Carbon\Carbon;

trait InvoiceNotificationTrait
{
    public function checkInvoicesOnLogin()
    {
        if ($this->student) {
            $today = Carbon::today();
            $statusToCheck = ['pendente', 'overdue', 'partial'];
            
            // Faturas vencidas
            $overdueInvoices = Invoice::where('student_id', $this->student->id)
                ->whereIn('status', $statusToCheck)
                ->whereDate('due_date', '<=', $today)
                ->get();
            
            foreach ($overdueInvoices as $invoice) {
                $this->notify(new InvoiceReminder($invoice, 'overdue'));
            }
            
            // Faturas que vencem em 3 dias
            $upcomingInvoices = Invoice::where('student_id', $this->student->id)
                ->whereIn('status', $statusToCheck)
                ->whereDate('due_date', $today->copy()->addDays(3))
                ->get();
            
            foreach ($upcomingInvoices as $invoice) {
                $this->notify(new InvoiceReminder($invoice, 'upcoming_3days'));
            }
        }
    }
}