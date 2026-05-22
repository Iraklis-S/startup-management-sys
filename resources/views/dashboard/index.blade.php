@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Dashboard</h1>
</div>

<div class="row g-3">
    @php
        $cards = [
            ['title' => 'Kompanitë', 'route' => 'kompanite.index'],
            ['title' => 'Personat', 'route' => 'personat.index'],
            ['title' => 'Raundet', 'route' => 'raundet.index'],
            ['title' => 'Investimet', 'route' => 'investimet.index'],
            ['title' => 'Fondet', 'route' => 'fondet.index'],
            // ['title' => 'Blerjet', 'route' => 'blerjet.index'],
            // ['title' => 'IPO', 'route' => 'ipos.index'],
            // ['title' => 'Arritjet', 'route' => 'arritjet.index'],
            ['title' => 'Zyrat', 'route' => 'zyrat.index'],
            // ['title' => 'Marrëdhëniet', 'route' => 'marredheniet.index'],
            // ['title' => 'Edukimet', 'route' => 'edukimet.index'],
            ['title' => 'KPI', 'route' => 'kpi.index'],
        ];
    @endphp

    @foreach($cards as $card)
        <div class="col-md-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5>{{ $card['title'] }}</h5>
                    <a href="{{ route($card['route']) }}" class="btn btn-sm btn-outline-primary">Hap</a>
                </div>
            </div>
        </div>
    @endforeach
</div>
@endsection