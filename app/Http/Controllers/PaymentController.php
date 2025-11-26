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
        return view('payments.create', compact('invoice'));
    }

    public function store(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->balance,
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,card,mobile_money',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'reference' => $request->reference,
            'notes' => $request->notes,
        ]);

        // Atualizar o status da fatura
        $invoice->amount_paid += $request->amount;
        
        if ($invoice->amount_paid >= $invoice->total_amount) {
            $invoice->status = 'paid';
        } elseif ($invoice->due_date < now() && $invoice->status === 'pending') {
            $invoice->status = 'overdue';
        }
        
        $invoice->save();

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
        return view('payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,card,mobile_money',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $payment->update($request->all());

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Pagamento atualizado com sucesso.');
    }

    public function destroy(Payment $payment)
    {
        $invoice = $payment->invoice;
        $payment->delete();

        // Recalcular o status da fatura
        $totalPaid = $invoice->payments()->sum('amount');
        $invoice->amount_paid = $totalPaid;
        
        if ($totalPaid >= $invoice->total_amount) {
            $invoice->status = 'paid';
        } elseif ($invoice->due_date < now()) {
            $invoice->status = 'overdue';
        } else {
            $invoice->status = 'pending';
        }
        
        $invoice->save();

        return redirect()->route('payments.index')
            ->with('success', 'Pagamento eliminado com sucesso.');
    }
}