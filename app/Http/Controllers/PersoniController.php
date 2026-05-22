<?php

namespace App\Http\Controllers;

use App\Models\Kompania;
use App\Models\Personi;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PersoniController extends Controller
{
    public function index()
    {
        $query = Personi::query();

        if ($q = request('q')) {
            $query->where(function($s) use ($q) {
                $s->where('first_name', 'like', "%{$q}%")
                  ->orWhere('last_name', 'like', "%{$q}%")
                  ->orWhere('affiliation_name', 'like', "%{$q}%");
            });
            // Prioritize exact full-name matches or exact first/last name
            $query->orderByRaw("CASE WHEN CONCAT(first_name, ' ', last_name) = ? THEN 0 WHEN first_name = ? OR last_name = ? THEN 1 WHEN CONCAT(first_name, ' ', last_name) LIKE ? THEN 2 ELSE 3 END", [$q, $q, $q, $q.'%']);
        }

        $personat = $query->latest()->paginate(20)->withQueryString();

        return view('personat.index', compact('personat'));
    }
    public function create()
    {
        $kompanite = Kompania::orderBy('name')->limit(200)->get(['id', 'name']);
        return view('personat.create', compact('kompanite'));
    }
    public function show(Personi $personi)
    {
        return view('personat.show', compact('personi'));
    }
    public function edit(Personi $personi)
    {

        return view('personat.edit', compact('personi'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => ['required', 'integer', 'exists:kompanite,id', Rule::unique('personat', 'company_id')],
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'birthplace' => 'nullable|string|max:150',
            'affiliation_name' => 'nullable|string|max:255',
        ]);

        Personi::create($validated);

        return redirect()->route('personat.index')->with('success', 'Personi u krijua me sukses.');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Personi $personi)
    {
        $validated = $request->validate([
            'company_id' => ['required', 'integer', 'exists:kompanite,id', Rule::unique('personat', 'company_id')->ignore($personi->id)],
            'first_name' => 'nullable|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'birthplace' => 'nullable|string|max:150',
            'affiliation_name' => 'nullable|string|max:255',
        ]);

        $personi->update($validated);

        return redirect()->route('personat.index')->with('success', 'Personi u përditësua me sukses.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Personi $personi)
    {
        $personi->delete();

        return redirect()->route('personat.index')->with('success', 'Personi u fshi me sukses.');
    }

    /**
     * Search persons for Select2 dropdown
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');
        
        $results = Personi::where('first_name', 'like', "%{$query}%")
            ->orWhere('last_name', 'like', "%{$query}%")
            ->select('company_id', 'first_name', 'last_name')
            ->limit(20)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->company_id,
                    'text' => trim($item->first_name . ' ' . $item->last_name),
                ];
            });

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => false],
        ]);
    }
}
