<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRaundiRequest;
use App\Models\Kompania;
use App\Models\RaundiFinancimit;
use Illuminate\Http\Request;

class RaundiFinancimitController extends Controller
{
    public function index()
    {
        $query = RaundiFinancimit::query()->with('kompania');

        // Search by company name
        if ($search = request('search')) {
            // join kompanite to allow ordering by exact match
            $query->whereHas('kompania', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
            $query->join('kompanite', 'raundet_financimit.company_id', '=', 'kompanite.id')
                  ->select('raundet_financimit.*')
                  ->orderByRaw("CASE WHEN kompanite.name = ? THEN 0 WHEN kompanite.name LIKE ? THEN 1 ELSE 2 END", [$search, $search.'%']);
        }

        // Filter by funding type
        if ($fundingType = request('funding_type')) {
            $query->where('funding_round_type', $fundingType);
        }

        // Sorting
        $sortBy = request('sort_by', '-funded_at');
        if ($sortBy == '-funded_at') {
            $query->orderBy('funded_at', 'desc');
        } elseif ($sortBy == 'funded_at') {
            $query->orderBy('funded_at', 'asc');
        } elseif ($sortBy == '-raised_amount') {
            $query->orderBy('raised_amount_usd', 'desc');
        }

        if (! request('search')) {
            $raundet = $query->paginate(15)->withQueryString();
        } else {
            $raundet = $query->paginate(15)->withQueryString();
        }
        $fundingTypes = RaundiFinancimit::distinct()->pluck('funding_round_type');

        return view('raundet.index', compact('raundet', 'fundingTypes'));
    }
    public function create()
    {

        return view('raundet.create');
    }


    public function show(RaundiFinancimit $raundi)
    {
        $raundi = $raundi->load('kompania', 'investimet');
        return view('raundet.show', compact('raundi'));
    }
    public function edit(RaundiFinancimit $raundi)
    {

        return view('raundet.edit', compact('raundi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRaundiRequest $request)
    {
        $data = $request->validated();

        RaundiFinancimit::create($data);

        return redirect()->route('raundet.index')->with('success', 'Raundi financiar u krijua me sukses.');
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(StoreRaundiRequest $request, RaundiFinancimit $raundi)
    {
        $data = $request->validated();

        $raundi->update($data);

        return redirect()->route('raundet.index')->with('success', 'Raundi financiar u përditësua me sukses.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RaundiFinancimit $raundi)
    {
        $raundi->delete();

        return redirect()->route('raundet.index')->with('success', 'Raundi financiar u fshi me sukses.');
    }

    /**
     * Search funding rounds for Select2 dropdown
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        $results = RaundiFinancimit::whereHas('kompania', function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%");
        })
            ->with('kompania')
            ->select('id', 'company_id', 'funding_round_type', 'raised_amount_usd')
            ->limit(20)
            ->get()
            ->map(function ($item) {
                $label = $item->kompania->name . ' - ' . ($item->funding_round_type ?? 'Funding Round');
                if ($item->raised_amount_usd) {
                    $label .= ' ($' . number_format($item->raised_amount_usd, 0) . ')';
                }
                return [
                    'id' => $item->id,
                    'text' => $label,
                ];
            });

        return response()->json([
            'results' => $results,
            'pagination' => ['more' => false],
        ]);
    }
}
