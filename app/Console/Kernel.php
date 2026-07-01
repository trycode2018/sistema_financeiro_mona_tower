<?php

namespace App\Console;

use App\Services\BillingService;
use App\Models\Invoice;
use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Processar cobranças em massa - uma vez por mês no dia 1 às 00:01
        $schedule->call(function () {
            $service = new BillingService();
            if ($service->isCobrancaAtiva()) {
                $service->processarCobrancaEmMassa();
            }
        })->monthlyOn(1, '00:01');

        // Aplicar juros de mora - uma vez por dia às 00:01
        $schedule->command('invoices:apply-late-fees')->dailyAt('00:01')->withoutOverlapping()->appendOutputTo(storage_path('logs/apply-late-fees.log'));

        // Enviar lembretes de faturas - uma vez por dia às 08:00
        $schedule->command('invoices:send-reminders')
            ->dailyAt('08:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/invoice-reminders.log'));

        // Enviar lembretes de faturas - a cada minuto
        //$schedule->command('invoices:send-reminders')
        //    ->everyMinute()
        //    ->withoutOverlapping()
        //    ->appendOutputTo(storage_path('logs/invoice-reminders.log'));

        // Verificar faturas vencidas - 9h
        $schedule->command('invoices:check-overdue')
            ->dailyAt('09:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/check-overdue.log'));

        // Verificar faturas vencidas - 15h
        $schedule->command('invoices:check-overdue')
            ->dailyAt('15:00')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/check-overdue.log'));

        // Limpar notificações lidas há mais de 15 dias
        $schedule->call(function () {
            \Illuminate\Notifications\DatabaseNotification::whereNotNull('read_at')
                ->where('read_at', '<', now()->subDays(15))
                ->delete();
        })->dailyAt('02:00')
          ->appendOutputTo(storage_path('logs/cleanup-notifications.log'));
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}