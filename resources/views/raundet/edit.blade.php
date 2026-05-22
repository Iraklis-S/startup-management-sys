@php
    $title = 'Raundet Financiar';
    $route = 'raundet';

@endphp

@extends('layouts.app')

@section('title', 'Përditëso Raund')

@section('content')
    <h1 class="h3 mb-4">Përditëso Raund Financiar</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route($route . '.update', ['raundi' => $raundi]) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="company_id" class="form-label">Kompania</label>
                    <select name=\"company_id\" id="company_id" class="form-control"></select>

                    @error('company_id')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Lloji i Raundit</label>
                    <select name="funding_round_type" class="form-select">
                        <option value="">{{ old('funding_round_type') ?? '' }}</option>
                        <option value="seed" {{ old('funding_round_type') == 'seed' ? 'selected' : '' }}>Seed</option>
                        <option value="series-a" {{ old('funding_round_type') == 'series-a' ? 'selected' : '' }}>Series A
                        </option>
                        <option value="series-b" {{ old('funding_round_type') == 'series-b' ? 'selected' : '' }}>Series B
                        </option>
                        <option value="series-c" {{ old('funding_round_type') == 'series-c' ? 'selected' : '' }}>Series C
                        </option>
                        <option value="angel" {{ old('funding_round_type') == 'angel' ? 'selected' : '' }}>Angel
                        </option>
                        <option value="grant" {{ old('funding_round_type') == 'grant' ? 'selected' : '' }}>Grant
                        </option>
                        <option value="debt_financing"
                            {{ old('funding_round_type') == 'debt_financing' ? 'selected' : '' }}>Debt Financing
                        </option>
                        <option value="private_equity"
                            {{ old('funding_round_type') == 'private_equity' ? 'selected' : '' }}>Private Equity
                        </option>
                    </select>
                    @error('funding_round_type')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Data e Financimit</label>
                    <input type="date" name="funded_at" class="form-control"
                        value="{{ old('funded_at', $raundi->funded_at?->format('Y-m-d')) }}">
                    @error('funded_at')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Shuma e Ngritur (USD)</label>
                            <input type="number" step="0.01" name="raised_amount_usd" class="form-control"
                                value="{{ old('raised_amount_usd', $raundi->raised_amount_usd) }}">
                            @error('raised_amount_usd')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Valorizimi Para Investimit</label>
                            <input type="number" step="0.01" name="pre_money_valuation_usd" class="form-control"
                                value="{{ old('pre_money_valuation_usd', $raundi->pre_money_valuation_usd) }}">
                            @error('pre_money_valuation_usd')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Përditëso</button>
                    <a href="{{ route($route . '.index') }}" class="btn btn-secondary">Kthehu</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        $('#company_id').select2({
            placeholder: 'Kerko kompanine...',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route('api.kompanite.search') }}',
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });
    </script>
@endsection
