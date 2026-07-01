<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Service;
use App\Models\Guardian;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index()
    {
        $students = Student::with([
            'guardian',
            'services'
        ])
        ->latest()
        ->paginate(6);

        return view('students.index', compact('students'));
    }

    public function create()
    {
        $guardians = Guardian::all();

        $services = Service::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('students.create', compact(
            'guardians',
            'services'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_code' => 'required|string|unique:students',

            'name' => 'required|string|max:255',

            'email' => 'required|email|unique:students',

            'class' => 'required|string|max:50',

            'academic_year' => 'required|string|max:20',

            'guardian_id' => 'required|exists:guardians,id',

            'services' => 'nullable|array',

            'services.*' => 'exists:services,id',
        ]);

        $student = Student::create([
            'student_code' => $validated['student_code'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'class' => $validated['class'],
            'academic_year' => $validated['academic_year'],
            'guardian_id' => $validated['guardian_id'],
        ]);

        if ($request->filled('services')) {

            $student->services()->attach(
                $request->services
            );

        }

        return redirect()
            ->route('students.index')
            ->with('success', 'Estudante criado com sucesso.');
    }

    public function show(Student $student)
    {
        $student->load([
            'guardian',
            'services',
            'invoices.payments'
        ]);

        return view('students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $guardians = Guardian::all();

        $services = Service::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('students.edit', compact(
            'student',
            'guardians',
            'services'
        ));
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'student_code' => 'required|string|unique:students,student_code,' . $student->id,

            'name' => 'required|string|max:255',

            'email' => 'required|email|unique:students,email,' . $student->id,

            'class' => 'required|string|max:50',

            'academic_year' => 'required|string|max:20',

            'guardian_id' => 'required|exists:guardians,id',

            'services' => 'nullable|array',

            'services.*' => 'exists:services,id',
        ]);

        $student->update([
            'student_code' => $validated['student_code'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'class' => $validated['class'],
            'academic_year' => $validated['academic_year'],
            'guardian_id' => $validated['guardian_id'],
        ]);

        $student->services()->sync(
            $request->services ?? []
        );

        return redirect()
            ->route('students.index')
            ->with('success', 'Estudante actualizado com sucesso.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()
            ->route('students.index')
            ->with('success', 'Estudante eliminado com sucesso.');
    }
}