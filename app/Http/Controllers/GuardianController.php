<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuardianController extends Controller
{
    public function index()
    {
        $guardians = Guardian::latest()->paginate(6);
        return view('guardians.index', compact('guardians'));
    }

    public function create()
    {
        return view('guardians.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:guardians',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'relationship' => 'required|string|max:50',
        ]);

        $data = $request->all();
        $data['created_by'] = Auth::id();
        
        Guardian::create($data);

        return redirect()->route('guardians.index')
            ->with('success', 'Encarregado de educação criado com sucesso.');
    }

    public function show(Guardian $guardian)
    {
        $guardian->load('students.invoices.payments');
        return view('guardians.show', compact('guardian'));
    }

    public function edit(Guardian $guardian)
    {
        return view('guardians.edit', compact('guardian'));
    }

    public function update(Request $request, Guardian $guardian)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:guardians,email,' . $guardian->id,
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'relationship' => 'required|string|max:50',
        ]);

        $data = $request->all();
        $data['updated_by'] = Auth::id();

        $guardian->update($data);

        return redirect()->route('guardians.index')
            ->with('success', 'Encarregado de educação atualizado com sucesso.');
    }

    public function destroy(Guardian $guardian)
    {
        $guardian->delete();

        return redirect()->route('guardians.index')
            ->with('success', 'Encarregado de educação eliminado com sucesso.');
    }
}