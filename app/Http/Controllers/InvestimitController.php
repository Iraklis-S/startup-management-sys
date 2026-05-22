<?php

namespace App\Http\Controllers;

use App\Models\Investim;
use App\Models\Kompania;
use App\Models\RaundiFinancimit;
use Illuminate\Http\Request;


class InvestimitController extends Controller
{
    public function index()
    {
        $query = Investim::query()->with('investorKompania', 'fundedKompania', 'raundiFinancimit');

        // Search by company name
        if ($search = request('search')) {
            // filter by related company names
            $query->whereHas('fundedKompania', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhereHas('investorKompania', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });

            // join companies to prioritize exact matches in either side
            $query->leftJoin('kompanite as fk', 'investimet.funded_company_id', '=', 'fk.id')
                  ->leftJoin('kompanite as ik', 'investimet.investor_company_id', '=', 'ik.id')
                  ->select('investimet.*')
                  ->orderByRaw(
                      "CASE WHEN fk.name = ? OR ik.name = ? THEN 0 WHEN fk.name LIKE ? OR ik.name LIKE ? THEN 1 ELSE 2 END",
                      [$search, $search, $search.'%', $search.'%']
                  );
        }

        // Sorting
        $sortBy = request('sort_by', '-created_at');
        if ($sortBy == '-created_at') {
            $query->latest();
        } else {
            $query->oldest();
        }

        $investimet = $query->paginate(15)->withQueryString();
        return view('investimet.index', compact('investimet'));
    }
    public function create()
    {
        $raundet = RaundiFinancimit::with('kompania')->orderBy('funded_at', 'desc')->limit(200)->get();
        $kompanite = Kompania::orderBy('name')->limit(200)->get(['id', 'name']);
        return view('investimet.create', compact('raundet', 'kompanite'));
    }
    public function edit(Investim $investim)
    {
        $raundet = RaundiFinancimit::with('kompania')->orderBy('funded_at', 'desc')->limit(200)->get();
        $kompanite = Kompania::orderBy('name')->limit(200)->get(['id', 'name']);
        return view('investimet.edit', compact('investim', 'raundet', 'kompanite'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'funding_round_id' => 'required|integer|exists:raundet_financimit,id',
            'funded_company_id' => 'required|integer|exists:kompanite,id',
            'investor_company_id' => 'required|integer|exists:kompanite,id',
        ]);

        Investim::create($validated);

        return redirect()->route('investimet.index')->with('success', 'Investimi u krijua me sukses.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Investim $investim)
    {
        $investim->load('investorKompania', 'fundedKompania', 'raundiFinancimit.kompania');

        return view('investimet.show', compact('investim'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Investim $investim)
    {
        $validated = $request->validate([
            'funding_round_id' => 'required|integer|exists:raundet_financimit,id',
            'funded_company_id' => 'required|integer|exists:kompanite,id',
            'investor_company_id' => 'required|integer|exists:kompanite,id',
        ]);

        $investim->update($validated);

        return redirect()->route('investimet.index')->with('success', 'Investimi u përditësua me sukses.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Investim $investim)
    {
        $investim->delete();

        return redirect()->route('investimet.index')->with('success', 'Investimi u fshi me sukses.');
    }
}
