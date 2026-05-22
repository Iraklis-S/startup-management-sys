<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Edukim;
use App\Models\Personi;
use Illuminate\Http\Request;

class EdukimitController extends Controller
{
    public function index()
    {
        $edukimet = Edukim::latest()->paginate(10);
        return view('edukimet.index', compact('edukimet'));
    }

    public function create()
    {
        $personat = Personi::orderBy('first_name')->limit(200)->get(['id','company_id', 'first_name', 'last_name']);
        return view('edukimet.create', compact('personat'));
    }

    public function edit(Edukim $edukim)
    {
        $current = Personi::find($edukim->person_id ?? $edukim->company_id);
        $personat = collect();
        if ($current) {
            $personat->push($current);
        }
        $personat = $personat->concat(Personi::orderBy('first_name')->limit(200)->get(['id','company_id', 'first_name', 'last_name']));
        return view('edukimet.edit', compact('edukim', 'personat'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|integer|exists:personat,id',
            'degree_type' => 'nullable|string|max:100',
            'subject' => 'nullable|string|max:150',
            'institution' => 'nullable|string|max:255',
            'graduated_at' => 'nullable|date',
        ]);

        Edukim::create($validated);

        return redirect()->route('edukimet.index')->with('success', 'Edukimi u krijua me sukses.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Edukim $edukim)
    {
        return redirect()->route('edukimet.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Edukim $edukim)
    {
        $validated = $request->validate([
            'company_id' => 'required|integer|exists:personat,id',
            'degree_type' => 'nullable|string|max:100',
            'subject' => 'nullable|string|max:150',
            'institution' => 'nullable|string|max:255',
            'graduated_at' => 'nullable|date',
        ]);

        $edukim->update($validated);

        return redirect()->route('edukimet.index')->with('success', 'Edukimi u përditësua me sukses.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Edukim $edukim)
    {
        $edukim->delete();

        return redirect()->route('edukimet.index')->with('success', 'Edukimi u fshi me sukses.');
    }
}
