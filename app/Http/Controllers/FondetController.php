<?php

namespace App\Http\Controllers;

use App\Models\Fond;
use App\Models\Kompania;
use Illuminate\Http\Request;

class FondetController extends Controller
{
    public function index()
    {
        $query = Fond::query()->with('kompania');
        
        if ($search = request('search')) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhereHas('kompania', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });

            // join kompanite to prioritize exact company name matches
            $query->leftJoin('kompanite as k', 'fondet.company_id', '=', 'k.id')
                  ->select('fondet.*')
                  ->orderByRaw("CASE WHEN k.name = ? THEN 0 WHEN k.name LIKE ? THEN 1 ELSE 2 END", [$search, $search.'%']);
        }

        $sortBy = request('sort_by', '-funded_at');
        if ($sortBy == '-funded_at') {
            $query->orderBy('funded_at', 'desc');
        } else {
            $query->oldest();
        }

        $fondet = $query->paginate(15)->withQueryString();
        return view('fondet.index', compact('fondet'));
    }

    public function create()
    {
        $kompanite = Kompania::orderBy('name')->limit(200)->get(['id', 'name']);
        return view('fondet.create', compact('kompanite'));
    }

    public function show(Fond $fondi)
    {
        $fondi->load('kompania');
        return view('fondet.show', compact('fondi'));
    }

    public function edit(Fond $fondi)
    {
        $current = Kompania::find($fondi->company_id);
        $kompanite = collect();
        if ($current) {
            $kompanite->push($current);
        }
        $kompanite = $kompanite->concat(Kompania::orderBy('name')->limit(200)->get(['id', 'name']));
        return view('fondet.edit', compact('fondi', 'kompanite'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fund_id' => 'nullable|string|max:100',
            'company_id' => 'required|integer|exists:kompanite,id',
            'name' => 'nullable|string|max:255',
            'funded_at' => 'nullable|date',
            'raised_amount' => 'nullable|numeric',
            'raised_currency_code' => 'nullable|string|max:10',
            'source_url' => 'nullable|string|max:500',
            'source_description' => 'nullable|string',
        ]);

        Fond::create($validated);

        return redirect()->route('fondet.index')->with('success', 'Fondi u krijua me sukses.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Fond $fondi)
    {
        $validated = $request->validate([
            'fund_id' => 'nullable|string|max:100',
            'company_id' => 'required|integer|exists:kompanite,id',
            'name' => 'nullable|string|max:255',
            'funded_at' => 'nullable|date',
            'raised_amount' => 'nullable|numeric',
            'raised_currency_code' => 'nullable|string|max:10',
            'source_url' => 'nullable|string|max:500',
            'source_description' => 'nullable|string',
        ]);

        $fondi->update($validated);

        return redirect()->route('fondet.index')->with('success', 'Fondi u përditësua me sukses.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Fond $fondi)
    {
        $fondi->delete();

        return redirect()->route('fondet.index')->with('success', 'Fondi u fshi me sukses.');
    }
}
