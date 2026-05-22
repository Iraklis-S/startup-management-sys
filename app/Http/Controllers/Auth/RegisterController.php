<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Personi;
use App\Models\Kompania;
use App\Models\Roli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    /**
     * Show the register form
     */
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    /**
     * Handle user registration
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
        ]);

        // Get the "End User" role (assuming role_id = 5 is end user based on the migration comment)
        $endUserRole = Roli::where('role_name', 'end_user')->first();
        if (!$endUserRole) {
            // Fallback to role_id 5 if role doesn't exist
            $endUserRole = Roli::findOrFail(5);
        }

        // Create a Kompania record for the Person (type: person)
        $personKompania = Kompania::create([
            'company_type' => 'other',
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'normalized_name' => strtolower($validated['first_name'] . ' ' . $validated['last_name']),
            'status' => 'pending',
            'verification_status' => 'pending',
        ]);

        // Create Personi record
        $personi = Personi::create([
            'company_id' => $personKompania->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'verification_status' => 'pending',
        ]);

        // Create User record
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $endUserRole->id,
            'person_id' => $personi->id,
            'verification_status' => 'pending',
        ]);

        // Log in the user
        auth()->login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Registration successful! Your account is pending verification.');
    }

    /**
     * Show the claim/register company form for end users
     */
    public function showClaimCompanyForm()
    {
        $this->authorize('viewClaimCompany');

        return view('auth.claim-company');
    }

    /**
     * Handle company claiming request
     */
    public function claimCompany(Request $request)
    {
        $this->authorize('viewClaimCompany');

        $validated = $request->validate([
            'action' => ['required', 'in:claim,register'],
            'company_id' => ['required_if:action,claim', 'exists:kompanite,id'],
            'company_name' => ['required_if:action,register', 'string', 'max:255'],
            'title' => ['required', 'string', 'max:150'],
        ]);

        $user = auth()->user();
        $personi = $user->personi;

        if (!$personi) {
            return back()->withErrors(['error' => 'User profile not found.']);
        }

        if ($validated['action'] === 'claim') {
            // User is claiming an existing company
            $kompania = Kompania::findOrFail($validated['company_id']);

            // Create relationship with C-Suite Executive title
            $marredhenia = \App\Models\Marredhenie::create([
                'person_id' => $personi->company_id,
                'company_id' => $kompania->id,
                'title' => $validated['title'] ?? 'C-Suite Executive',
                'start_at' => now()->toDateString(),
                'verification_status' => 'pending',
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Company claim request submitted for verification!');
        } else {
            // User is registering a new company
            $newKompania = Kompania::create([
                'company_type' => 'startup',
                'name' => $validated['company_name'],
                'normalized_name' => strtolower($validated['company_name']),
                'status' => 'pending',
                'verification_status' => 'pending',
            ]);

            // Create relationship as founder/CEO
            $marredhenia = \App\Models\Marredhenie::create([
                'person_id' => $personi->company_id,
                'company_id' => $newKompania->id,
                'title' => $validated['title'] ?? 'Founder/CEO',
                'start_at' => now()->toDateString(),
                'verification_status' => 'pending',
            ]);

            return redirect()->route('dashboard')
                ->with('success', 'Company registered and pending verification!');
        }
    }
}
