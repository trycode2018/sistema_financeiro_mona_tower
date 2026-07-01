<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Notifications\InvoiceReminder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendInvoiceReminders extends Command
{
    protected $signature = 'invoices:send-reminders';

    protected $description =
        'Envia lembretes de faturas a vencer e vencidas';

    public function handle()
    {
        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | Faturas que vencem em 3 dias
        |--------------------------------------------------------------------------
        */

        $upcomingInvoices3 = Invoice::with([
                'student.guardian'
            ])
            ->where('status', 'pendente')
            ->whereDate(
                'due_date',
                $today->copy()->addDays(3)
            )
            ->get();

        foreach ($upcomingInvoices3 as $invoice) {

            $guardian = $invoice->student?->guardian;

            if ($guardian && $guardian->email) {

                $guardian->notify(
                    new InvoiceReminder(
                        $invoice,
                        'upcoming_3days'
                    )
                );

                $this->info(
                    "Lembrete enviado para {$guardian->email}"
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Faturas que vencem em 2 dias
        |--------------------------------------------------------------------------
        */

        $upcomingInvoices2 = Invoice::with([
                'student.guardian'
            ])
            ->where('status', 'pendente')
            ->whereDate(
                'due_date',
                $today->copy()->addDays(2)
            )
            ->get();

        foreach ($upcomingInvoices2 as $invoice) {

            $guardian = $invoice->student?->guardian;

            if ($guardian && $guardian->email) {

                $guardian->notify(
                    new InvoiceReminder(
                        $invoice,
                        'upcoming_2days'
                    )
                );

                $this->info(
                    "Lembrete enviado para {$guardian->email}"
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Faturas que vencem em 1 dias
        |--------------------------------------------------------------------------
        */

        $upcomingInvoices1 = Invoice::with([
                'student.guardian'
            ])
            ->where('status', 'pendente')
            ->whereDate(
                'due_date',
                $today->copy()->addDays(1)
            )
            ->get();

        foreach ($upcomingInvoices1 as $invoice) {

            $guardian = $invoice->student?->guardian;

            if ($guardian && $guardian->email) {

                $guardian->notify(
                    new InvoiceReminder(
                        $invoice,
                        'upcoming_1day'
                    )
                );

                $this->info(
                    "Lembrete enviado para {$guardian->email}"
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Faturas que vencem hoje
        |--------------------------------------------------------------------------
        */

        $upcomingInvoices = Invoice::with([
                'student.guardian'
            ])
            ->where('status', 'pendente')
            ->whereDate(
                'due_date',
                $today->copy()->addDays(0)
            )
            ->get();

        foreach ($upcomingInvoices as $invoice) {

            $guardian = $invoice->student?->guardian;

            if ($guardian && $guardian->email) {

                $guardian->notify(
                    new InvoiceReminder(
                        $invoice,
                        'today'
                    )
                );

                $this->info(
                    "Lembrete enviado para {$guardian->email}"
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Faturas vencidas
        |--------------------------------------------------------------------------
        */

        $overdueInvoices = Invoice::with([
            'student.guardian'
        ])
        ->where('status', 'vencido')
        ->whereDate('due_date', '<', $today)
        ->get();

        foreach ($overdueInvoices as $invoice) {

            $guardian = $invoice->student?->guardian;

            if ($guardian && $guardian->email) {

                $guardian->notify(
                    new InvoiceReminder(
                        $invoice,
                        'vencido'
                    )
                );

                $this->info(
                    "Notificação enviada para {$guardian->email}"
                );
            }
        }

        // =========================================================================
        // Faturas PARCIAIS (status = 'parcial')
        // =========================================================================
        // Vencem em 3 dias
        $upcoming3parcial = Invoice::with(['student.guardian'])
            ->where('status', 'parcial')
            ->whereDate('due_date', $today->copy()->addDays(3))
            ->get();

        foreach ($upcoming3parcial as $invoice) {
            $guardian = $invoice->student?->guardian;

            if($guardian && $guardian->email) {
                $guardian->notify(
                    new InvoiceReminder(
                        $invoice,
                        'parcial_upcoming_3days'
                    )
                );

                $this->info(
                    "Lembrete enviado para {$guardian->email} (parcial)"
                );
            }

            $this->notifyGuardian($invoice, 'parcial_upcoming_3days');
        }

        // Vencem em 2 dias
        $upcoming2parcial = Invoice::with(['student.guardian'])
            ->where('status', 'parcial')
            ->whereDate('due_date', $today->copy()->addDays(2))
            ->get();

            $guardian = $invoice->student?->guardian;

            if($guardian && $guardian->email) {
                $guardian->notify(
                    new InvoiceReminder(
                        $invoice,
                        'parcial_upcoming_2days'
                    )
                );

                $this->info(
                    "Lembrete enviado para {$guardian->email} (parcial)"
                );
            }

        foreach ($upcoming2parcial as $invoice) {
            $this->notifyGuardian($invoice, 'parcial_upcoming_2days');
        }

        // Vencem em 1 dia
        $upcoming1parcial = Invoice::with(['student.guardian'])
            ->where('status', 'parcial')
            ->whereDate('due_date', $today->copy()->addDays(1))
            ->get();

            $guardiam = $invoice->student?->guardian;

            if($guardian && $guardian->email) {
                $guardian->notify(
                    new InvoiceReminder(
                        $invoice,
                        'parcial_upcoming_1day'
                    )
                );

                $this->info(
                    "Lembrete enviado para {$guardian->email} (parcial)"
                );
            }

        foreach ($upcoming1parcial as $invoice) {
            $this->notifyGuardian($invoice, 'parcial_upcoming_1day');
        }

        // Vencem hoje
        $todayParcial = Invoice::with(['student.guardian'])
            ->where('status', 'parcial')
            ->whereDate('due_date', $today)
            ->get();

            $guardian = $invoice->student?->guardian;

            if($guardian && $guardian->email) {
                $guardian->notify(
                    new InvoiceReminder(
                        $invoice,
                        'parcial_today'
                    )
                );

                $this->info(
                    "Lembrete enviado para {$guardian->email} (parcial)"
                );
            }

        foreach ($todayParcial as $invoice) {
            $this->notifyGuardian($invoice, 'parcial_today');
        }

        // Vencidas (parciais)
        $overdueParcial = Invoice::with(['student.guardian'])
            ->where('status', 'parcial')
            ->whereDate('due_date', '<', $today)
            ->get();

                $guardian = $invoice->student?->guardian;
    
                if($guardian && $guardian->email) {
                    $guardian->notify(
                        new InvoiceReminder(
                            $invoice,
                            'parcial_vencido'
                        )
                    );
    
                    $this->info(
                        "Notificação enviada para {$guardian->email} (parcial)"
                    );
                }

        foreach ($overdueParcial as $invoice) {
            $this->notifyGuardian($invoice, 'parcial_vencido');
        }

        $this->info(
            'Lembretes enviados com sucesso!'
        );
        $this->info('Upcoming3 invoices: ' . $upcomingInvoices3->count());
        $this->info('Upcoming2 invoices: ' . $upcomingInvoices2->count());
        $this->info('Upcoming1 invoices: ' . $upcomingInvoices1->count());
        $this->info('Upcoming invoices: ' . $upcomingInvoices->count());
        $this->info('Overdue invoices: ' . $overdueInvoices->count());

        return Command::SUCCESS;
    }
}