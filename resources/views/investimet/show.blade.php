@php
    $title = 'Investimet';
    $route = 'investimet';
@endphp

@extends('layouts.app')

@section('title', 'Detajet e Investimit')

@section('content')
    <h1 class="h3 mb-4">Detajet e Investimit</h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Informacioni i Investimit</h5>
                </div>
                <div class="card-body">
                 
                    <p><strong>ID:</strong> {{ $investim->id }}</p>
                    <p><strong>Raundi Financiar:</strong> {{ $investim->raundiFinancimit->kompania->name ?? 'N/A' }} -
                        {{ $investim->raundiFinancimit->funding_round_type ?? 'N/A' }}</p>
                    <p><strong>Data e Raundit:</strong> {{ $investim->raundiFinancimit->funded_at ?? '---' }}</p>
                    <p><strong>Shuma e Ngritur (USD):</strong>
                        @if ($investim->raundiFinancimit->raised_amount_usd)
                            ${{ number_format($investim->raundiFinancimit->raised_amount_usd, 0) }}
                        @else
                            ---
                        @endif
                    </p>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Palët e Përfshira</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Investitori</h6>
                            <p><strong>{{ $investim->investorKompania->name }}</strong></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Kompania që financohet</h6>
                            <p><strong>{{ $investim->fundedKompania->name }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route($route . '.edit', $investim->id) }}" class="btn btn-warning">Redakto</a>
        <a href="{{ route($route . '.index') }}" class="btn btn-secondary">Kthehu</a>
    </div>
@endsection
