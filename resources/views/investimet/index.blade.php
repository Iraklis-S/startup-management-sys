@php
    $title = 'Investimet';
    $route = 'investimet';
    
@endphp

@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ $title }}</h1>
        <a href="{{ route($route . '.create') }}" class="btn btn-primary">+ Shto Investim</a>
    </div>

    <!-- Filtering Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route($route . '.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Kërkoni Kompani</label>
                    <input type="text" name="search" class="form-control" placeholder="Emri i kompanisë..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">SORT BY</label>
                    <select name="sort_by" class="form-select">
                        <option value="-created_at" {{ request('sort_by') == '-created_at' ? 'selected' : '' }}>Më të reja</option>
                        <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Më të vjetra</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filtro</button>
                </div>
                @if(request('search') || request('sort_by'))
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <a href="{{ route($route . '.index') }}" class="btn btn-secondary w-100">Reseto</a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0 bg-white">
                <thead class="table-light">
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Investitori</th>
                        <th>Kompania (Financuar)</th>
                        <th>Raundi</th>
                        <th>Shuma (USD)</th>
                        <th style="width: 200px;">Veprime</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($investimet as $investim)
                        <tr>
                            <td><small>{{ $investim->id }}</small></td>
                            <td>{{ $investim->investorKompania->name ?? '---' }}</td>
                            <td>{{ $investim->fundedKompania->name ?? '---' }}</td>
                            <td>{{ $investim->raundiFinancimit->funding_round_type ?? '---' }}</td>
                            <td>
                                @if($investim->raundiFinancimit?->raised_amount_usd)
                                    ${{ number_format($investim->raundiFinancimit->raised_amount_usd, 0) }}
                                @else
                                    ---
                                @endif
                            </td>
                            <td>
                                <a href="{{ route($route . '.show', $investim->id) }}" class="btn btn-sm btn-info">Show</a>
                                <a href="{{ route($route . '.edit', $investim->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route($route . '.destroy', $investim->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('A je i sigurt?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Nuk ka investime në sistem.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($investimet->count())
            <div class="card-footer bg-light">
                {{ $investimet->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
