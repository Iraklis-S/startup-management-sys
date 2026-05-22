<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Zyra;
use Illuminate\Http\Request;

class ZyratController extends Controller
{

    public function index()
    {
        $zyrat = Zyra::latest()->paginate(10);
        return view('zyrat.index', compact('zyrat'));
    }
    public function create()
    {
        //needs a way to search companies
        return view('zyrat.create');
    }
    public function edit(Zyra $zyra)
    {
        $zyra->load('kompania');
        // $company = $zyra->kompania ? ['id' => $zyra->kompania->id, 'name' => $zyra->kompania->name] : null;
        return view('zyrat.edit', compact('zyra'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|integer|exists:kompanite,id',
            'office_id' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'region' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        Zyra::create($validated);

        return redirect()->route('zyrat.index')->with('success', 'Zyra u krijua me sukses.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Zyra $zyra)
    {
        return redirect()->route('zyrat.index');
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Zyra $zyra)
    {
        $validated = $request->validate([
            'company_id' => 'required|integer|exists:kompanite,id',
            'office_id' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'region' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $zyra->update($validated);

        return redirect()->route('zyrat.index')->with('success', 'Zyra u përditësua me sukses.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Zyra $zyra)
    {
        $zyra->delete();

        return redirect()->route('zyrat.index')->with('success', 'Zyra u fshi me sukses.');
    }
}
