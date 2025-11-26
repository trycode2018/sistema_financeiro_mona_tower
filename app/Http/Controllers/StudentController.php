<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Guardian;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with('guardian')->latest()->paginate(10);
        return view('students.index', compact('students'));
    }

    public function create()
    {
        $guardians = Guardian::all();
        return view('students.create', compact('guardians'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_code' => 'required|string|unique:students',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students',
            'class' => 'required|string|max:50',
            'academic_year' => 'required|string|max:20',
            'guardian_id' => 'required|exists:guardians,id',
            'transport_required' => 'boolean',
        ]);

        Student::create($request->all());

        return redirect()->route('students.index')
            ->with('success', 'Estudante criado com sucesso.');
    }

    public function show(Student $student)
    {
        $student->load(['guardian', 'invoices.payments']);
        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $guardians = Guardian::all();
        return view('students.edit', compact('student', 'guardians'));
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'student_code' => 'required|string|unique:students,student_code,' . $student->id,
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'class' => 'required|string|max:50',
            'academic_year' => 'required|string|max:20',
            'guardian_id' => 'required|exists:guardians,id',
            'transport_required' => 'boolean',
        ]);

        $student->update($request->all());

        return redirect()->route('students.index')
            ->with('success', 'Estudante atualizado com sucesso.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')
            ->with('success', 'Estudante eliminado com sucesso.');
    }
}