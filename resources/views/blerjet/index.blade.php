@php
    $title = 'Blerjet';
    $route = 'blerjet';
    $items = $blerjet ?? collect();
@endphp

@extends('layouts.app')

@section('title', $title)


@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ $title }}</h1>
        <a href="{{ route($route . '.create') }}" class="btn btn-primary">+ Shto Blerje</a>
    </div>

    <!-- Filtering Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route($route . '.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Kërkoni Kompani</label>
                    <input type="text" name="search" class="form-control" placeholder="Blerëse ose e blere..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">SORT BY sipas</label>
                    <select name="sort_by" class="form-select">
                        <option value="-acquired_at" {{ request('sort_by') == '-acquired_at' ? 'selected' : '' }}>Më të reja</option>
                        <option value="acquired_at" {{ request('sort_by') == 'acquired_at' ? 'selected' : '' }}>Më të vjetra</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filtro</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0 bg-white">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Kompania Blerëse</th>
                        <th>Kompania e Blere</th>
                        <th>Çmimi</th>
                        <th>Data</th>
                        <th style="width: 200px;">Veprime</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td><small>{{ $item->id }}</small></td>
                            <td>{{ $item->acquiringKompania->name ?? '---' }}</td>
                            <td>{{ $item->acquiredKompania->name ?? '---' }}</td>
                            <td>{{ $item->price_amount ? number_format($item->price_amount, 2) . ' ' . ($item->price_currency_code ?? 'USD') : '---' }}</td>
                            <td>{{ $item->acquired_at?->format('d.m.Y') ?? '---' }}</td>
                            <td>
                                <a href="{{ route($route . '.show', $item->id) }}" class="btn btn-sm btn-info">Shfaq</a>
                                <a href="{{ route($route . '.edit', $item->id) }}" class="btn btn-sm btn-warning">Redakto</a>
                                <form action="{{ route($route . '.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('A je i sigurt?')">Fshi</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Nuk ka blerje në sistem.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($items->count())
            <div class="card-footer bg-light">
                {{ $items->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

@endsection
