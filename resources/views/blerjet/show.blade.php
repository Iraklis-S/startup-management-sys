@php
    $title = 'Blerje';
    $route = 'blerjet';
@endphp

@extends('layouts.app')

@section('title', 'Detajet e Blerjës')

@section('content')
    <h1 class="h3 mb-4">Detajet e Blerjës</h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Informacioni i Blerjës</h5>
                </div>
                <div class="card-body">
                    <p><strong>ID:</strong> {{ $blerje->id }}</p>
                    <p><strong>Kompania Blerëse:</strong> {{ $blerje->acquiringKompania->name ?? 'N/A' }}</p>
                    <p><strong>Kompania e Blere:</strong> {{ $blerje->acquiredKompania->name ?? 'N/A' }}</p>
                    <p><strong>Data e Blerjës:</strong> {{ $blerje->acquired_at?->format('d.m.Y') ?? '---' }}</p>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Detajet Financiare</h5>
                </div>
                <div class="card-body">
                    <p><strong>Çmimi:</strong> 
                        @if($blerje->price_amount)
                            {{ $blerje->price_amount }} {{ $blerje->price_currency_code ?? 'USD' }}
                        @else
                            ---
                        @endif
                    </p>
                    @if($blerje->source_url)
                        <p><strong>Burim:</strong> <a href="{{ $blerje->source_url }}" target="_blank">{{ $blerje->source_url }}</a></p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route($route . '.edit', $blerje->id) }}" class="btn btn-warning">Redakto</a>
        <a href="{{ route($route . '.index') }}" class="btn btn-secondary">Kthehu</a>
    </div>
@endsection
