@php
    $title = 'Raundet e Financimit';
    $route = 'raundet';
    $items = $raundet ?? collect();
@endphp

@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ $title }}</h1>
        <a href="{{ route($route . '.create') }}" class="btn btn-primary">+ Shto Raund</a>
    </div>

    <!-- Filtering Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route($route . '.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Kerkoni Kompani</label>
                    <input type="text" name="search" class="form-control" placeholder="Emri i kompanisë..."
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Lloji Raundi</label>
                    <select name="funding_type" class="form-select">
                        <option value="">Të gjitha</option>
                        @foreach ($fundingTypes as $type)
                            <option value="{{ $type }}" {{ request('funding_type') == $type ? 'selected' : '' }}>
                                {{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Radhit sipas</label>
                    <select name="sort_by" class="form-select">
                        <option value="-funded_at" {{ request('sort_by') == '-funded_at' ? 'selected' : '' }}>Më të reja
                        </option>
                        <option value="funded_at" {{ request('sort_by') == 'funded_at' ? 'selected' : '' }}>Më të vjetra
                        </option>
                        <option value="-raised_amount" {{ request('sort_by') == '-raised_amount' ? 'selected' : '' }}>Shuma
                            më e madhe</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Filtro</button>
                </div>
                @if (request('search') || request('funding_type') || request('sort_by'))
                    <div class="col-md-1">
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
                        <th>Kompania</th>
                        <th>Lloji</th>
                        <th>Data</th>
                        <th>Shuma (USD)</th>
                        <th style="width: 180px;">Veprime</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td><small>{{ $item->id }}</small></td>
                            <td>{{ $item->kompania->name ?? '---' }}</td>
                            <td>{{ $item->funding_round_type ?? '---' }}</td>
                            <td>{{ $item->funded_at?->format('d.m.Y') ?? '---' }}</td>
                            <td>
                                @if ($item->raised_amount_usd)
                                    ${{ number_format($item->raised_amount_usd, 0) }}
                                @else
                                    ---
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column align-items-center w-100">
                                    <a href="{{ route($route . '.show', $item->id) }}"
                                        class="btn btn-sm btn-info w-75 mb-2">Shfaq</a>

                                    <a href="{{ route($route . '.edit', $item->id) }}"
                                        class="btn btn-sm btn-warning w-75 mb-2">Edit</a>

                                    <form action="{{ route($route . '.destroy', $item->id) }}" method="POST"
                                        class="w-75 mb-0">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger w-100"
                                            onclick="return confirm('A je i sigurt?')">Delete</button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Nuk ka raunde financiare në sistem.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($items->count())
            <div class="card-footer bg-light">
                {{ $items->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
