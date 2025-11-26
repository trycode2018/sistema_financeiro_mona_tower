<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $payments = Payment::with(['invoice.student'])
            ->orderBy('payment_date', 'desc')
            ->paginate(10);

        return view('payments.index', compact('payments'));
    }

    public function create(Invoice $invoice)
    {
        $invoice->load(['student']);
        
        if (!$invoice->student) {
            return redirect()->route('invoices.edit', ['invoice' => $invoice->id])
                ->with('error', 'Esta fatura não tem um estudante associado.');
        }

        return view('payments.create', compact('invoice'));
    }

    public function store(Request $request, Invoice $invoice)
    {
        if (!$invoice->student) {
            return redirect()->route('invoices.edit', ['invoice' => $invoice->id])
                ->with('error', 'Esta fatura não tem um estudante associado.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->balance,
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,card,mobile_money',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'reference' => $request->reference,
            'notes' => $request->notes,
        ]);

        $this->updateInvoiceStatus($invoice);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Pagamento registado com sucesso.');
    }

    public function show(Payment $payment)
    {
        $payment->load(['invoice.student.guardian']);
        return view('payments.show', compact('payment'));
    }

    public function edit(Payment $payment)
    {
        $payment->load(['invoice.student']);
        return view('payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $invoice = $payment->invoice;
        $maxAmount = $invoice->total_amount - ($invoice->payments()->sum('amount') - $payment->amount);

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $maxAmount,
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,card,mobile_money',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $payment->update($request->all());
        $this->updateInvoiceStatus($invoice);

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Pagamento atualizado com sucesso.');
    }

    public function destroy(Payment $payment)
    {
        $invoice = $payment->invoice;
        $payment->delete();
        $this->updateInvoiceStatus($invoice);

        return redirect()->route('payments.index')
            ->with('success', 'Pagamento eliminado com sucesso.');
    }

    public function createFullPayment(Invoice $invoice)
    {
        $invoice->load(['student']);

        if (!$invoice->student) {
            return redirect()->route('invoices.edit', ['invoice' => $invoice->id])
                ->with('error', 'Esta fatura não tem um estudante associado.');
        }

        if ($invoice->balance <= 0) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Esta fatura já está totalmente paga.');
        }

        Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $invoice->balance,
            'payment_date' => now(),
            'payment_method' => 'cash',
            'reference' => 'Pagamento total automático',
            'notes' => 'Pagamento realizado automaticamente através do sistema.',
        ]);

        $this->updateInvoiceStatus($invoice);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Pagamento total registrado com sucesso!');
    }

    private function updateInvoiceStatus(Invoice $invoice)
    {
        $totalPaid = $invoice->payments()->sum('amount');
        $invoice->amount_paid = $totalPaid;

        if ($totalPaid >= $invoice->total_amount) {
            $invoice->status = 'paid';
        } elseif ($totalPaid > 0) {
            $invoice->status = 'partial';
        } elseif ($invoice->due_date < now()) {
            $invoice->status = 'overdue';
        } else {
            $invoice->status = 'pending';
        }
        
        $invoice->save();
    }
}