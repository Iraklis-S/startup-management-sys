<?php

namespace App\Http\Controllers;

use App\Models\Ipo;
use App\Models\Kompania;
use Illuminate\Http\Request;

class IpoController extends Controller
{
    public function index()
    {
        $query = Ipo::query()->with('kompania');
        
        if ($search = request('search')) {
            $query->whereHas('kompania', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });

            $query->leftJoin('kompanite as k', 'ipos.company_id', '=', 'k.id')
                  ->select('ipos.*')
                  ->orderByRaw("CASE WHEN k.name = ? THEN 0 WHEN k.name LIKE ? THEN 1 ELSE 2 END", [$search, $search.'%']);
        }

        $sortBy = request('sort_by', '-public_at');
        if ($sortBy == '-public_at') {
            $query->orderBy('public_at', 'desc');
        } else {
            $query->oldest();
        }

        $ipos = $query->paginate(15)->withQueryString();
        return view('ipos.index', compact('ipos'));
    }
    public function create()
    {
        $kompanite = Kompania::orderBy('name')->limit(200)->get(['id', 'name']);
        return view('ipos.create', compact('kompanite'));
    }
    public function edit(Ipo $ipo)
    {
        $current = Kompania::find($ipo->company_id);
        $kompanite = collect();
        if ($current) {
            $kompanite->push($current);
        }
        $kompanite = $kompanite->concat(Kompania::orderBy('name')->limit(200)->get(['id', 'name']));
        return view('ipos.edit', compact('ipo', 'kompanite'));
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|integer|exists:kompanite,id',
            'valuation_amount' => 'nullable|numeric',
            'valuation_currency_code' => 'nullable|string|max:10',
            'raised_amount' => 'nullable|numeric',
            'raised_currency_code' => 'nullable|string|max:10',
            'public_at' => 'nullable|date',
            'stock_symbol' => 'nullable|string|max:50',
            'source_url' => 'nullable|string|max:500',
        ]);

        Ipo::create($validated);

        return redirect()->route('ipos.index')->with('success', 'IPO u krijua me sukses.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ipo $ipo)
    {
        $ipo->load('kompania');
        return view('ipos.show', compact('ipo'));
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ipo $ipo)
    {
        $validated = $request->validate([
            'company_id' => 'required|integer|exists:kompanite,id',
            'valuation_amount' => 'nullable|numeric',
            'valuation_currency_code' => 'nullable|string|max:10',
            'raised_amount' => 'nullable|numeric',
            'raised_currency_code' => 'nullable|string|max:10',
            'public_at' => 'nullable|date',
            'stock_symbol' => 'nullable|string|max:50',
            'source_url' => 'nullable|string|max:500',
        ]);

        $ipo->update($validated);

        return redirect()->route('ipos.index')->with('success', 'IPO u përditësua me sukses.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ipo $ipo)
    {
        $ipo->delete();

        return redirect()->route('ipos.index')->with('success', 'IPO u fshi me sukses.');
    }
}
