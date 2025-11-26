<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Student;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with(['student', 'payments'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('invoices.index', compact('invoices'));
    }

    public function create()
    {
        $students = Student::with('guardian')->get();
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

        $invoice = Invoice::create([
            'invoice_number' => 'INV-' . Str::random(8),
            'student_id' => $request->student_id,
            'due_date' => $request->due_date,
            'issue_date' => now(),
            'total_amount' => $request->total_amount,
            'description' => $request->description,
        ]);

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['student.guardian', 'payments']);
        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice)
    {
        $students = Student::all();
        return view('invoices.edit', compact('invoice', 'students'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $request->validate([
            'due_date' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
            'description' => 'required|string|max:500',
        ]);

        $invoice->update($request->all());

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice)
    {
        $invoice->delete();

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }
}