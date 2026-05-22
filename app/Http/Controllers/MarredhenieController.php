<?php

namespace App\Http\Controllers;

use App\Models\Kompania;
use App\Models\Marredhenie;
use App\Models\Personi;
use Illuminate\Http\Request;

class MarredhenieController extends Controller
{

    public function index()
    {
        $marredheniet = Marredhenie::latest()->paginate(10);
        return view('marredheniet.index', compact('marredheniet'));
    }
    public function create()
    {
        $kompanite = Kompania::orderBy('name')->limit(200)->get(['id', 'name']);
        $personat = Personi::orderBy('first_name')->limit(200)->get(['company_id', 'first_name', 'last_name']);
        return view('marredheniet.create', compact('kompanite', 'personat'));
    }
    public function edit(Marredhenie $marredhenie)
    {
        $kompaniaCurrent = Kompania::find($marredhenie->company_id);
        $kompanite = collect();
        if ($kompaniaCurrent) {
            $kompanite->push($kompaniaCurrent);
        }
        $kompanite = $kompanite->concat(Kompania::orderBy('name')->limit(200)->get(['id', 'name']));

        $personCurrent = Personi::find($marredhenie->person_id);
        $personat = collect();
        if ($personCurrent) {
            $personat->push($personCurrent);
        }
        $personat = $personat->concat(Personi::orderBy('first_name')->limit(200)->get(['id','company_id', 'first_name', 'last_name']));

        return view('marredheniet.edit', compact('marredhenie', 'kompanite', 'personat'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'person_id' => 'required|integer|exists:personat,id',
            'company_id' => 'required|integer|exists:kompanite,id',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
            'is_past' => 'nullable|boolean',
            'sequence' => 'nullable|integer',
            'title' => 'nullable|string|max:150',
        ]);

        Marredhenie::create($validated);

        return redirect()->route('marredheniet.index')->with('success', 'Marredhenia u krijua me sukses.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Marredhenie $marredhenie)
    {
        return redirect()->route('marredheniet.index');
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Marredhenie $marredhenie)
    {
        $validated = $request->validate([
            'person_id' => 'required|integer|exists:personat,id',
            'company_id' => 'required|integer|exists:kompanite,id',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date',
            'is_past' => 'nullable|boolean',
            'sequence' => 'nullable|integer',
            'title' => 'nullable|string|max:150',
        ]);

        $marredhenie->update($validated);

        return redirect()->route('marredheniet.index')->with('success', 'Marredhenia u përditësua me sukses.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Marredhenie $marredhenie)
    {
        $marredhenie->delete();

        return redirect()->route('marredheniet.index')->with('success', 'Marredhenia u fshi me sukses.');
    }
}
