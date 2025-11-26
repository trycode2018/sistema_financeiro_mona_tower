<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['student'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $students = Student::with('guardian')->get();
        
        // Se não houver estudantes, redirecionar para criar um
        if ($students->isEmpty()) {
            return redirect()->route('students.create')
                ->with('error', 'É necessário criar pelo menos um estudante antes de criar uma fatura.');
        }

        return view('invoices.create', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'due_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:500',
        ]);

        // Verificar se o estudante realmente existe
        $student = Student::find($request->student_id);
        if (!$student) {
            return redirect()->back()
                ->with('error', 'Estudante selecionado não existe.')
                ->withInput();
        }

        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . Str::upper(Str::random(8)),
            'student_id' => $request->student_id,
            'due_date' => $request->due_date,
            'issue_date' => now(),
            'total_amount' => $request->total_amount,
            'description' => $request->description,
            'status' => 'pending',
            'amount_paid' => 0,
        ]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Fatura criada com sucesso.');
    }

    public function show(Invoice $invoice)
    {
        // Carregar as relações necessárias
        $invoice->load(['student.guardian', 'payments']);
        
        // Verificar se o estudante existe
        if (!$invoice->student) {
            return redirect()->route('invoices.edit', $invoice)
                ->with('error', 'Estudante associado a esta fatura não foi encontrado. Por favor, atualize a fatura.');
        }

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $students = Student::all();
        
        // Se não houver estudantes, redirecionar
        if ($students->isEmpty()) {
            return redirect()->route('students.create')
                ->with('error', 'É necessário criar pelo menos um estudante antes de editar a fatura.');
        }

        return view('invoices.edit', compact('invoice', 'students'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'due_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:500',
        ]);

        // Verificar se o estudante realmente existe
        $student = Student::find($request->student_id);
        if (!$student) {
            return redirect()->back()
                ->with('error', 'Estudante selecionado não existe.')
                ->withInput();
        }

        $invoice->update([
            'student_id' => $request->student_id,
            'due_date' => $request->due_date,
            'total_amount' => $request->total_amount,
            'description' => $request->description,
        ]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Fatura atualizada com sucesso.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Fatura eliminada com sucesso.');
    }
}