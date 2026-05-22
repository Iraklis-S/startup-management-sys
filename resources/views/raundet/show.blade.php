@php
    $title = 'Raundi Financiar';
    $route = 'raundet';
    /** @var \App\Models\RaundiFinancimit $raundi */
@endphp


@extends('layouts.app')

@section('title', 'Detajet e Raundit')

@section('content')
    <h1 class="h3 mb-4">Detajet e Raundit Financiar</h1>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Informacioni Bazik</h5>
                </div>
                <div class="card-body">
                    <p><strong>ID:</strong> {{ $raundi->id }}</p>
                    <p><strong>Kompania:</strong> {{ $raundi->kompania->name ?? 'N/A' }}</p>
                    <p><strong>Lloji i Raundit:</strong> {{ $raundi->funding_round_type ?? '---' }}</p>
                    <p><strong>Kodi i Raundit:</strong> {{ $raundi->funding_round_code ?? '---' }}</p>
                    <p><strong>Data e Financimit:</strong> {{ $raundi->funded_at }}</p>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Detajet Financiare</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Shuma e Ngritur (USD):</strong></p>
                            <p>
                                @if ($raundi->raised_amount_usd)
                                    ${{ number_format($raundi->raised_amount_usd, 0) }}
                                @else
                                    ---
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Vlera Paraprakisht (USD):</strong></p>
                            <p>
                                @if ($raundi->pre_money_valuation_usd)
                                    ${{ number_format($raundi->pre_money_valuation_usd, 0) }}
                                @else
                                    ---
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            @if ($raundi->investimet->count())
                <div class="card shadow-sm mt-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Investitorët ({{ $raundi->investimet->count() }})</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Investitori</th>
                                    <th>Te Dhena Investitori</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($raundi->investimet as $investim)
                                    <tr>
                                        <td>{{ $investim->investorKompania->name }}</td>
                                        <td><a href="/kompanite/{{ $investim->investorKompania->id }}">Link</a></td>


                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route($route . '.edit', $raundi->id) }}" class="btn btn-warning">Redakto</a>
        <a href="{{ route($route . '.index') }}" class="btn btn-secondary">Kthehu</a>
    </div>
@endsection
