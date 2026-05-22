@php
    $title = 'Raundet Financiar';
    $route = 'raundet';
@endphp

@extends('layouts.app')

@section('title', 'Shto Raund')

@section('content')


    <h1 class="h3 mb-4">Shto Raund Financiar</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route($route . '.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="company_id" class="form-label">Kompania</label>
                    <select name="company_id" id="company_id" class="form-control"></select>
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
                        <option value="debt_financing" {{ old('funding_round_type') == 'debt_financing' ? 'selected' : '' }}>Debt Financing
                        </option>
                        <option value="private_equity" {{ old('funding_round_type') == 'private_equity' ? 'selected' : '' }}>Private Equity
                        </option>
                    </select>
                    @error('funding_round_type')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Data e Financimit</label>
                    <input type="date" name="funded_at" class="form-control" value="{{ old('funded_at') }}">
                    @error('funded_at')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Shuma e Ngritur (USD)</label>
                            <input type="number" step="0.01" name="raised_amount_usd" class="form-control"
                                value="{{ old('raised_amount_usd') }}">
                            @error('raised_amount_usd')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Valorizimi Para Investimit</label>
                            <input type="number" step="0.01" name="pre_money_valuation_usd" class="form-control"
                                value="{{ old('pre_money_valuation_usd') }}">
                            @error('pre_money_valuation_usd')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Ruaj</button>
                    <a href="{{ route($route . '.index') }}" class="btn btn-secondary">Kthehu</a>
                </div>
            </form>
        </div>
    </div>


    <script>
      $(function() {
            // initialize Select2 (your existing config)
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
                },
                theme: 'bootstrap-5',
                width: '100%'
            });

            // minimal safety: ensure Select2 sets the <select> value on selection
            // (Select2 normally does this automatically; this line is harmless)
            $('#company_id').on('select2:select', function(e) {
                $(this).val(e.params.data.id).trigger('change');
            });
        });
    </script>
@endsection
