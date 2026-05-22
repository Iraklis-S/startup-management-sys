<?php

namespace App\Http\Controllers;

use App\Models\Kompania;
use App\Http\Requests\StoreKompaniaRequest;
use App\Http\Requests\UpdateKompaniaRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class KompaniaController extends Controller
{
    public function index()
    {
        $query = Kompania::query();

        if ($name = request('name')) {
            $query->where('name', 'like', "%{$name}%");
            // Prioritize exact matches first, then prefix matches, then others
            $query->orderByRaw("CASE WHEN name = ? THEN 0 WHEN name LIKE ? THEN 1 ELSE 2 END", [$name, $name.'%']);
        }
        if ($category = request('category_code')) {
            $query->where('category_code', $category);
        }
        if ($status = request('status')) {
            $query->where('status', $status);
        }
        // `entity_type` deprecated; use `company_type` filter if provided
        $ctype = request('company_type', null);
        if ($ctype === 'startup') {
            $query->where('company_type', 'startup');
        } elseif ($ctype === 'other') {
            // 'other' should show companies that are not startups (including null)
            $query->where(function ($q) {
                $q->whereNull('company_type')->orWhere('company_type', '<>', 'startup');
            });
        }

        // Ensure a deterministic fallback ordering
        if (! request('name')) {
            $query->latest();
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $kompanite = $query->paginate(15)->withQueryString();

        return view('kompanite.index', compact('kompanite'));
    }
    public function create()
    {
        return view('kompanite.create');
    }

    public function show(Kompania $kompania)
    {
        $kompania = $kompania->load([
            'parent',
            'raundetFinancimit',
            'personat',
            'fondet',
            'zyrat',
            'arritjet',
            'ipos',
            'blerjetSiBlerese',
            'blerjetSiEBlere',
        ]);

        return view('kompanite.show', compact('kompania'));
    }

    public function edit(Kompania $kompania)
    {
        return view('kompanite.edit',compact('kompania'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreKompaniaRequest $request)
    {
        $data = $request->validated();
        $data['verification_status'] = 'pending';
        Kompania::create($data);

        return redirect()->route('kompanite.index')->with('success', 'Kompania u krijua me sukses.');
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateKompaniaRequest $request, Kompania $kompania)
    {
        $data = $request->validated();
        $kompania->update($data);

        return redirect()->route('kompanite.index')->with('success', 'Kompania u përditësua me sukses.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Kompania $kompania)
    {
        $kompania->delete();

        return redirect()->route('kompanite.index')->with('success', 'Kompania u fshi me sukses.');
    }

    /**
     * Search companies for Select2 dropdown
     */
   

   
}
