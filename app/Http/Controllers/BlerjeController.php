<?php

namespace App\Http\Controllers;

use App\Models\Blerje;
use App\Models\Kompania;
use Illuminate\Http\Request;

class BlerjeController extends Controller
{
    public function index()
    {
        $query = Blerje::query()->with('acquiringKompania', 'acquiredKompania');

        if ($search = request('search')) {
            $query->whereHas('acquiringKompania', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('acquiredKompania', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });

            // Join companies to allow exact-match prioritization
            $query->leftJoin('kompanite as fk', 'blerjet.acquiring_company_id', '=', 'fk.id')
                  ->leftJoin('kompanite as ak', 'blerjet.acquired_company_id', '=', 'ak.id')
                  ->select('blerjet.*')
                  ->orderByRaw(
                      "CASE WHEN fk.name = ? OR ak.name = ? THEN 0 WHEN fk.name LIKE ? OR ak.name LIKE ? THEN 1 ELSE 2 END",
                      [$search, $search, $search.'%', $search.'%']
                  );
        }

        $sortBy = request('sort_by', '-acquired_at');
        if ($sortBy == '-acquired_at') {
            $query->orderBy('acquired_at', 'desc');
        } else {
            $query->oldest();
        }

        $blerjet = $query->paginate(15)->withQueryString();
        return view('blerjet.index', compact('blerjet'));
    }
    public function create()
    {
        $kompanite = Kompania::orderBy('name')->limit(200)->get(['id', 'name']);
        return view('blerjet.create', compact('kompanite'));
    }
    public function edit(Blerje $blerjet)
    {
        $acquiring = Kompania::find($blerjet->acquiring_company_id);
        $acquired = Kompania::find($blerjet->acquired_company_id);
        $kompanite = collect();
        if ($acquiring) $kompanite->push($acquiring);
        if ($acquired && (!$acquiring || $acquired->id !== $acquiring->id)) $kompanite->push($acquired);
        $kompanite = $kompanite->concat(Kompania::orderBy('name')->limit(200)->get(['id', 'name']));
        return view('blerjet.edit', compact('blerjet', 'kompanite'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'acquisition_id' => 'nullable|string|max:100',
            'acquiring_company_id' => 'required|integer|exists:kompanite,id',
            'acquired_company_id' => 'required|integer|exists:kompanite,id',
            'term_code' => 'nullable|string|max:50',
            'price_amount' => 'nullable|numeric',
            'price_currency_code' => 'nullable|string|max:10',
            'acquired_at' => 'nullable|date',
            'source_url' => 'nullable|string|max:500',
            'source_description' => 'nullable|string',
        ]);

        Blerje::create($validated);

        return redirect()->route('blerjet.index')->with('success', 'Blerja u krijua me sukses.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Blerje $blerje)
    {
        $blerje->load('acquiringKompania', 'acquiredKompania');
        return view('blerjet.show', compact('blerje'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blerje $blerje)
    {
        $validated = $request->validate([
            'acquisition_id' => 'nullable|string|max:100',
            'acquiring_company_id' => 'required|integer|exists:kompanite,id',
            'acquired_company_id' => 'required|integer|exists:kompanite,id',
            'term_code' => 'nullable|string|max:50',
            'price_amount' => 'nullable|numeric',
            'price_currency_code' => 'nullable|string|max:10',
            'acquired_at' => 'nullable|date',
            'source_url' => 'nullable|string|max:500',
            'source_description' => 'nullable|string',
        ]);

        $blerje->update($validated);

        return redirect()->route('blerjet.index')->with('success', 'Blerja u përditësua me sukses.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blerje $blerje)
    {
        $blerje->delete();

        return redirect()->route('blerjet.index')->with('success', 'Blerja u fshi me sukses.');
    }
}
