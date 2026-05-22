@php
    $title = 'Arritjet';
    $route = 'arritjet';
    $items = $arritjet ?? collect();
@endphp

@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ $title }}</h1>
        <a href="{{ route($route . '.create') }}" class="btn btn-primary">+ Shto Arritje</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route($route . '.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Kërko Kompani</label>
                    <input type="text" name="search" class="form-control" placeholder="Emri i kompanisë" value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Radhit sipas</label>
                    <select name="sort_by" class="form-select">
                        <option value="-milestone_at" {{ request('sort_by') == '-milestone_at' ? 'selected' : '' }}>Data më e re</option>
                        <option value="milestone_at" {{ request('sort_by') == 'milestone_at' ? 'selected' : '' }}>Data më e vjetër</option>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <label class="form-label invisible">Filtro</label>
                    <button type="submit" class="btn btn-primary">Filtro</button>
                </div>
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
                        <th>Milestone</th>
                        <th>Data</th>
                        <th style="width: 220px;">Veprime</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ optional($item->kompania)->name ?? '---' }}</td>
                            <td>{{ $item->milestone_code ?? '---' }}</td>
                            <td>{{ optional($item->milestone_at)->format('Y-m-d') ?? '---' }}</td>
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
                            <td colspan="5" class="text-center py-4 text-muted">Nuk ka arritje të regjistruara.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($items->hasPages())
            <div class="card-footer bg-light">
                {{ $items->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
