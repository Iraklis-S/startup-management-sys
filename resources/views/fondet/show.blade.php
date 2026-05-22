@php
    $title = 'Fondet';
    $route = 'fondet';
@endphp

@extends('layouts.app')

@section('title', 'Detajet e Fondit')

@section('content')
    <h1 class="h3 mb-4">Detajet e Fondit</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <p><strong>ID:</strong> {{ $fondi->id }}</p>
            <p><strong>Kompania:</strong> {{ optional($fondi->kompania)->name ?? '---' }}</p>
            <p><strong>Emri i Fondit:</strong> {{ $fondi->name ?? '---' }}</p>
            <p><strong>Data e Fondit:</strong> {{ optional($fondi->funded_at)->format('Y-m-d') ?? '---' }}</p>
            <p><strong>Shuma e Mbledhur:</strong> {{ $fondi->raised_amount ? number_format($fondi->raised_amount, 2) . ' ' . ($fondi->raised_currency_code ?? 'USD') : '---' }}</p>
            <p><strong>URL:</strong> @if($fondi->source_url)<a href="{{ $fondi->source_url }}" target="_blank">{{ $fondi->source_url }}</a>@else --- @endif</p>
            <p><strong>Përshkrim:</strong> {{ $fondi->source_description ?? '---' }}</p>
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route($route . '.edit', $fondi->id) }}" class="btn btn-warning">Redakto</a>
        <a href="{{ route($route . '.index') }}" class="btn btn-secondary">Kthehu</a>
    </div>
@endsection
