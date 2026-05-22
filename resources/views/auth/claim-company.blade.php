@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Kërkesa për Kompani ose Fond</h4>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('claim-company.post') }}">
                        @csrf

                        <div class="mb-4">
                            <label class="form-label fw-bold">Çfarë dëshironi të bëni?</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="action" id="claim" value="claim" 
                                    onclick="toggleClaimRegister()" {{ old('action') === 'claim' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="claim">
                                    Kërkesa për të përfaqësuar një kompani/fond ekzistuese
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="action" id="register" value="register"
                                    onclick="toggleClaimRegister()" {{ old('action') === 'register' ? 'checked' : '' }} required>
                                <label class="form-check-label" for="register">
                                    Regjistro një kompani të re
                                </label>
                            </div>
                        </div>

                        <!-- Claim Company Section -->
                        <div id="claimSection" class="mb-4" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Kompania ose Fondi</label>
                                <select name=\"company_id\" class="form-control @error('company_id') is-invalid @enderror">
                                    <option value="">Zgjedh kompani...</option>
                                    @foreach(\App\Models\Kompania::where('company_type', 'startup')->orWhere('category_code', 'fund')->get() as $kompania)
                                        <option value="{{ $kompania->id }}" {{ old('company_id') == $kompania->id ? 'selected' : '' }}>
                                            {{ $kompania->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('company_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Register Company Section -->
                        <div id="registerSection" class="mb-4" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label">Emri i Kompanisë</label>
                                <input type="text" name="company_name" class="form-control @error('company_name') is-invalid @enderror"
                                    placeholder="Emri i kompanisë" value="{{ old('company_name') }}">
                                @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Common Fields -->
                        <div class="mb-3">
                            <label class="form-label">Pozita/Roli</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                                placeholder="p.sh. CEO, Co-Founder, Investor" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Dërgoni Kërkesën</button>
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">Anulo</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleClaimRegister() {
    const action = document.querySelector('input[name="action"]:checked').value;
    const claimSection = document.getElementById('claimSection');
    const registerSection = document.getElementById('registerSection');
    
    if (action === 'claim') {
        claimSection.style.display = 'block';
        registerSection.style.display = 'none';
    } else {
        claimSection.style.display = 'none';
        registerSection.style.display = 'block';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleClaimRegister();
});
</script>
@endsection
