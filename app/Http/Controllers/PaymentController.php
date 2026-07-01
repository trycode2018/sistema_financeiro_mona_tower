<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use App\Models\Invoice;
use Illuminate\Http\Request;
use App\Traits\HasAuditLog;
use App\Notifications\PaymentConfirmed;
use App\Notifications\PaymentReceived;
use App\Notifications\PaymentRejected;
use Illuminate\Support\Facades\Notification;

class PaymentController extends Controller
{
    use HasAuditLog;

    /**
     * Confirmar um pagamento pendente
     */
    public function confirm($id)
    {
        $payment = Payment::findOrFail($id);
        
        // Verificar se o pagamento já foi confirmado
        if ($payment->status === 'confirmed') {
            return back()->with('error', 'Este pagamento já foi confirmado.');
        }

        $old = $payment->getOriginal();

        // Atualizar o status do pagamento
        $payment->update([
            'status' => 'confirmed'
        ]);

        // ATUALIZAR O amount_paid DA FATURA SOMANDO TODOS OS PAGAMENTOS CONFIRMADOS
        $invoice = $payment->invoice;
        $totalConfirmed = $invoice->payments()->where('status', 'confirmed')->sum('amount');
        $invoice->amount_paid = $totalConfirmed;
        
        // Atualizar o status da fatura baseado no novo valor pago
        $this->updateInvoiceStatus($invoice);

        // Registrar log de auditoria
        $this->logActivity(
            'pagamento_confirmado',
            $payment,
            $old,
            $payment->getChanges()
        );

        // Notificar os administradores e equipe financeira
        $users = User::whereIn('role', ['admin', 'financeiro'])->get();
        Notification::send($users, new PaymentConfirmed($payment));
        
        // Notificar o estudante e responsável (opcional)
        if ($invoice->student && $invoice->student->guardian) {
            Notification::send($invoice->student->guardian, new PaymentConfirmed($payment));
        }

        return back()->with('success', 'Pagamento confirmado com sucesso.');
    }

    /**
     * Rejeitar um pagamento pendente
     */
    public function reject($id)
    {
        $payment = Payment::findOrFail($id);
        
        // Verificar se o pagamento já foi confirmado ou rejeitado
        if ($payment->status !== 'pending') {
            return back()->with('error', 'Este pagamento não pode ser rejeitado pois já foi ' . $payment->status);
        }

        $old = $payment->getOriginal();

        $payment->update([
            'status' => 'rejected'
        ]);

        // Pagamento rejeitado NÃO entra no cálculo do amount_paid
        $invoice = $payment->invoice;
        $totalConfirmed = $invoice->payments()->where('status', 'confirmed')->sum('amount');
        $invoice->amount_paid = $totalConfirmed;
        
        // Atualizar o status da fatura (sem o pagamento rejeitado)
        $this->updateInvoiceStatus($invoice);

        // Registrar log de auditoria
        $this->logActivity(
            'pagamento_rejeitado',
            $payment,
            $old,
            $payment->getChanges()
        );

        // Notificar os utilizadores com perfil de gestão
        $users = User::whereIn('role', ['admin', 'financeiro', 'secretaria'])->get();
        Notification::send($users, new PaymentRejected($payment, $invoice));

        return back()->with('success', 'Pagamento rejeitado com sucesso.');
    }

    /**
     * Listar todos os pagamentos
     */
    public function index()
    {
        $payments = Payment::with(['invoice.student'])->latest()->paginate(10);
        return view('payments.index', compact('payments'));
    }

    /**
     * Mostrar formulário para criar um novo pagamento
     */
    public function create(Invoice $invoice)
    {
        $invoice->load(['student', 'payments']);
        
        if (!$invoice->student) {
            return redirect()->route('invoices.edit', ['invoice' => $invoice->id])
                ->with('error', 'Esta fatura não tem um estudante associado.');
        }

        // Calcular o saldo REAL (apenas pagamentos confirmados)
        $totalConfirmed = $invoice->payments()
            ->where('status', 'confirmed')
            ->sum('amount');
        
        $realBalance = $invoice->total_amount - $totalConfirmed;
        
        if ($realBalance <= 0) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Esta fatura já está totalmente paga.');
        }
        
        // Verificar se existe pagamento pendente
        $hasPending = $invoice->payments()
            ->where('status', 'pending')
            ->exists();
        

        return view('payments.create', compact('invoice', 'realBalance'));
    }

    /**
     * Armazenar um novo pagamento
     */
    public function store(Request $request, Invoice $invoice)
    {
        if (!$invoice->student) {
            return redirect()->route('invoices.edit', ['invoice' => $invoice->id])
                ->with('error', 'Esta fatura não tem um estudante associado.');
        }

        // Calcular o saldo REAL (apenas pagamentos confirmados)
        $totalConfirmed = $invoice->payments()
            ->where('status', 'confirmed')
            ->sum('amount');
        
        $realBalance = $invoice->total_amount - $totalConfirmed;
        
        if ($realBalance <= 0) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Esta fatura já está totalmente paga.');
        }

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $realBalance,
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,card,mobile_money',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        // Criar o pagamento com status 'pending'
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
            'payment_date' => $request->payment_date,
            'payment_method' => $request->payment_method,
            'reference' => $request->reference,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        // Atualizar o status da fatura
        $this->updateInvoiceStatus($invoice);

        // Notificar administradores
        $users = User::whereIn('role', ['admin', 'financeiro'])->get();
        Notification::send($users, new PaymentReceived($payment, $invoice));

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Pagamento registado. Aguarda confirmação.');
    }

    /**
     * Mostrar detalhes de um pagamento específico
     */
    public function show(Payment $payment)
    {
        $payment->load(['invoice.student.guardian']);
        return view('payments.show', compact('payment'));
    }

    /**
     * Mostrar formulário para editar um pagamento
     */
    public function edit(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return redirect()->route('payments.show', $payment)
                ->with('error', 'Apenas pagamentos pendentes podem ser editados.');
        }

        $payment->load(['invoice.student']);
        
        $invoice = $payment->invoice;
        $totalConfirmed = $invoice->payments()
            ->where('status', 'confirmed')
            ->where('id', '!=', $payment->id)
            ->sum('amount');
        
        $maxAmount = $invoice->total_amount - $totalConfirmed;

        return view('payments.edit', compact('payment', 'maxAmount'));
    }

    /**
     * Atualizar um pagamento pendente
     */
    public function update(Request $request, Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return redirect()->route('payments.show', $payment)
                ->with('error', 'Apenas pagamentos pendentes podem ser editados.');
        }

        $invoice = $payment->invoice;
        
        $totalConfirmed = $invoice->payments()
            ->where('status', 'confirmed')
            ->where('id', '!=', $payment->id)
            ->sum('amount');
        
        $maxAmount = $invoice->total_amount - $totalConfirmed;

        $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $maxAmount,
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,card,mobile_money',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        $old = $payment->getOriginal();
        $payment->update($request->all());
        
        $this->logActivity(
            'pagamento_atualizado',
            $payment,
            $old,
            $payment->getChanges()
        );
        
        $this->updateInvoiceStatus($invoice);

        return redirect()->route('payments.show', $payment)
            ->with('success', 'Pagamento atualizado com sucesso.');
    }

    /**
     * Eliminar um pagamento pendente
     */
    public function destroy(Payment $payment)
    {
        if ($payment->status !== 'pending') {
            return redirect()->route('payments.index')
                ->with('error', 'Apenas pagamentos pendentes podem ser eliminados.');
        }

        $invoice = $payment->invoice;
        $payment->delete();
        $this->updateInvoiceStatus($invoice);

        return redirect()->route('payments.index')
            ->with('success', 'Pagamento eliminado com sucesso.');
    }

    /**
     * Criar um pagamento total automático
     */
    public function createFullPayment(Invoice $invoice)
    {
        $invoice->load(['student', 'payments']);

        if (!$invoice->student) {
            return redirect()->route('invoices.edit', ['invoice' => $invoice->id])
                ->with('error', 'Esta fatura não tem um estudante associado.');
        }

        $totalConfirmed = $invoice->payments()
            ->where('status', 'confirmed')
            ->sum('amount');
        
        $realBalance = $invoice->total_amount - $totalConfirmed;

        if ($realBalance <= 0) {
            return redirect()->route('invoices.show', $invoice)
                ->with('error', 'Esta fatura já está totalmente paga.');
        }

        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'amount' => $realBalance,
            'payment_date' => now(),
            'payment_method' => 'cash',
            'reference' => 'Pagamento total automático',
            'notes' => 'Pagamento realizado automaticamente através do sistema.',
            'status' => 'pending',
        ]);

        $this->updateInvoiceStatus($invoice);

        $users = User::whereIn('role', ['admin', 'financeiro'])->get();
        Notification::send($users, new PaymentReceived($payment, $invoice));

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Pagamento total registrado. Aguarda confirmação.');
    }

    /**
     * Atualizar o status da fatura com base nos pagamentos
     */
    private function updateInvoiceStatus(Invoice $invoice)
    {
        $invoice->load('payments');
        
        $totalConfirmed = $invoice->payments()
            ->where('status', 'confirmed')
            ->sum('amount');
        
        $hasPending = $invoice->payments()
            ->where('status', 'pending')
            ->exists();
        
        $invoice->amount_paid = $totalConfirmed;
        
        if ($totalConfirmed >= $invoice->total_amount) {
            $invoice->status = 'pago';
        } elseif ($hasPending) {
            $invoice->status = 'aguardando_confirmacao';
        } elseif ($totalConfirmed > 0) {
            $invoice->status = 'parcial';
        } elseif ($invoice->due_date < now()) {
            $invoice->status = 'vencido';
        } else {
            $invoice->status = 'pendente';
        }
        
        $invoice->save();
    }

    /**
     * Registrar atividade no log de auditoria
     */
    protected function logActivity($action, $model, $oldValues = null, $newValues = null)
    {
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'auditable_type' => get_class($model),
            'auditable_id' => $model->id,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}