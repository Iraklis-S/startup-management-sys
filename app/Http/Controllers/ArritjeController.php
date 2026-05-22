<?php

namespace App\Http\Controllers;

use App\Models\Arritje;
use App\Models\Kompania;
use Illuminate\Http\Request;

class ArritjeController extends Controller
{
    public function index()
    {
        $query = Arritje::query()->with('kompania');
        
        if ($search = request('search')) {
            $query->whereHas('kompania', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });

            $query->leftJoin('kompanite as k', 'arritjet.company_id', '=', 'k.id')
                  ->select('arritjet.*')
                  ->orderByRaw("CASE WHEN k.name = ? THEN 0 WHEN k.name LIKE ? THEN 1 ELSE 2 END", [$search, $search.'%']);
        }

        $sortBy = request('sort_by', '-milestone_at');
        if ($sortBy == '-milestone_at') {
            $query->orderBy('milestone_at', 'desc');
        } else {
            $query->oldest();
        }

        $arritjet = $query->paginate(15)->withQueryString();
        return view('arritjet.index', compact('arritjet'));
    }
    public function create()
    {
        $kompanite = Kompania::orderBy('name')->limit(200)->get(['id', 'name']);
        return view('arritjet.create', compact('kompanite'));
    }
    public function edit(Arritje $arritje)
    {
        $current = Kompania::find($arritje->company_id);
        $kompanite = collect();
        if ($current) {
            $kompanite->push($current);
        }
        $kompanite = $kompanite->concat(Kompania::orderBy('name')->limit(200)->get(['id', 'name']));
        return view('arritjet.edit', compact('arritje', 'kompanite'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|integer|exists:kompanite,id',
            'milestone_at' => 'nullable|date',
            'milestone_code' => 'nullable|string|max:100',
            'source_url' => 'nullable|string|max:500',
            'source_description' => 'nullable|string',
        ]);

        Arritje::create($validated);

        return redirect()->route('arritjet.index')->with('success', 'Arritja u krijua me sukses.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Arritje $arritje)
    {
        $arritje->load('kompania');
        return view('arritjet.show', compact('arritje'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Arritje $arritje)
    {
        $validated = $request->validate([
            'company_id' => 'required|integer|exists:kompanite,id',
            'milestone_at' => 'nullable|date',
            'milestone_code' => 'nullable|string|max:100',
            'source_url' => 'nullable|string|max:500',
            'source_description' => 'nullable|string',
        ]);

        $arritje->update($validated);

        return redirect()->route('arritjet.index')->with('success', 'Arritja u përditësua me sukses.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Arritje $arritje)
    {
        $arritje->delete();

        return redirect()->route('arritjet.index')->with('success', 'Arritja u fshi me sukses.');
    }
}
