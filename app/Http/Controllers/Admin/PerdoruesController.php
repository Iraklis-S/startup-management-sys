<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PerdoruesController extends Controller
{
    public function index()
    {
        $perdoruesit = User::latest()->paginate(10);
        return view('admin.perdoruesit.index', compact('perdoruesit'));
    }

    public function create()
    {
        $rolet = \App\Models\Roli::all();
        $personat = \App\Models\Personi::select('id','first_name','last_name')->get();
        $kompanite = \App\Models\Kompania::select('id','name')->get();
        return view('admin.perdoruesit.create', compact('rolet','personat','kompanite'));
    }

    public function edit(User $perdoruesit)
    {
        $rolet = \App\Models\Roli::all();
        $personat = \App\Models\Personi::select('id','first_name','last_name')->get();
        $kompanite = \App\Models\Kompania::select('id','name')->get();
        return view('admin.perdoruesit.edit', compact('perdoruesit','rolet','personat','kompanite'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'nullable|integer|exists:rolet,id',
            'person_id' => 'nullable|integer|exists:personat,id',
            'kompani_id' => 'nullable|integer|exists:kompanite,id',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        User::create($validated);

        return redirect()->route('admin.perdoruesit.index')->with('success', 'Perdoruesi u krijua me sukses.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $perdorues)
    {
        return redirect()->route('admin.perdoruesit.index');
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $perdorues)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($perdorues->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role_id' => 'nullable|integer|exists:rolet,id',
            'person_id' => 'nullable|integer|exists:personat,id',
            'kompani_id' => 'nullable|integer|exists:kompanite,id',
            'is_active' => 'nullable|boolean',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = bcrypt($validated['password']);
        }

        $perdorues->update($validated);

        return redirect()->route('admin.perdoruesit.index')->with('success', 'Perdoruesi u përditësua me sukses.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $perdorues)
    {
        $perdorues->is_active = false;
        $perdorues->save();

        return redirect()->route('admin.perdoruesit.index')->with('success', 'Perdoruesi u fshi me sukses.');
    }
}
