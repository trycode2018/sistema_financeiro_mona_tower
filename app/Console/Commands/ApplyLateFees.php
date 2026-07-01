<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ApplyLateFees extends Command
{
    protected $signature = 'invoices:apply-late-fees';

    protected $description = 'Aplica taxas às facturas vencidas';

    public function handle()
    {
        $invoices = Invoice::where('status', 'vencido')
            ->whereDate('due_date', '<', Carbon::today())
            ->where('late_fee_applied', false)
            ->get();

        foreach ($invoices as $invoice) {
            $fee = 2000;
            $invoice->update([
                'late_fee' => $fee,
                'late_fee_applied' => true,
                'total_amount' => $invoice->total_amount + $fee,
            ]);

            $this->info("Taxa aplicada na factura {$invoice->id}");
        }

        return Command::SUCCESS;
    }
}