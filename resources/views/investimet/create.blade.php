@php
    $title = 'Investimet';
    $route = 'investimet';
@endphp

@extends('layouts.app')

@section('title', 'Shto ' . $title)

@section('content')
    <h1 class="h3 mb-4">Shto {{ $title }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route($route . '.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Raundi Financimit *</label>
                    <select name="funding_round_id" class="form-select searchable-select" data-placeholder="Kërkoni raund..."
                        required>
                        <option value=""></option>
                        @foreach ($raundet as $raund)
                            <option value="{{ $raund->id }}"
                                {{ old('funding_round_id') == $raund->id ? 'selected' : '' }}>
                                {{ $raund->kompania->name ?? 'N/A' }} - {{ $raund->funding_round_type ?? 'Round' }}
                            </option>
                        @endforeach
                    </select>
                    @error('funding_round_id')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Kompania qe merr Investim *</label>
                    <select name="funded_company_id" id="funded_company_id" class="form-select"
                        required>
                    </select>
                    @error('funded_company_id')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Investitori *</label>
                   <select name="investor_company_id" id="investor_company_id" class="form-select"
                        required>
                    </select>
                
                    @error('investor_company_id')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Ruaj Investimin</button>
                    <a href="{{ route($route . '.index') }}" class="btn btn-secondary">Kthehu</a>
                </div>
            </form>
        </div>
    </div>
    <script>
        $('#funded_company_id').select2({
            placeholder: function() {
                return $(this).data('placeholder') || 'Kërko...';
            },
            minimumInputLength: 2,
            ajax: {
                url: '{{ route('api.kompanite.search') }}',
                dataType: 'json',
                delay: 300,
                data: params => ({
                    q: params.term
                }),
                processResults: data => ({
                    results: data.results
                }),
                cache: true
            },
            theme: 'bootstrap-5',
            width: '100%'
        });
        $('#investor_company_id').select2({
            placeholder: function() {
                return $(this).data('placeholder') || 'Kërko...';
            },
            minimumInputLength: 2,
            ajax: {
                url: '{{ route('api.kompanite.search') }}',
                dataType: 'json',
                delay: 300,
                data: params => ({
                    q: params.term
                }),
                processResults: data => ({
                    results: data.results
                }),
                cache: true
            },
            theme: 'bootstrap-5',
            width: '100%'
        });
    </script>
@endsection
