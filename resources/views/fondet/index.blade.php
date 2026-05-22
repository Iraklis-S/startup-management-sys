@php
    $title = 'Fondet';
    $route = 'fondet';
   
@endphp

@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex flex-column">
            <h1 class="h3">{{ $title }}</h1>
            <h4 class="h6">Fondet ekzistojne si entitet qe jane pjese e nje kompanie.</h4>
        </div>
        <a href="{{ route($route . '.create') }}" class="btn btn-primary">+ Shto Fond</a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route($route . '.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Kërko Fond / Kompani</label>
                    <input type="text" name="search" class="form-control" placeholder="Emri i fondit ose kompanisë"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">SORT BY </label>
                    <select name="sort_by" class="form-select">
                        <option value="-funded_at" {{ request('sort_by') == '-funded_at' ? 'selected' : '' }}>Data më e re
                        </option>
                        <option value="funded_at" {{ request('sort_by') == 'funded_at' ? 'selected' : '' }}>Data më e vjetër
                        </option>
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
                        <th>Fond</th>
                        <th>Kompania</th>
                        <th>Shuma</th>
                        <th>Data</th>
                        <th style="width: 220px;">Veprime</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fondet as $fondi)
                        <tr>
                            <td>{{ $fondi->id }}</td>
                            <td>{{ $fondi->name ?? '---' }}</td>
                            <td>{{ optional($fondi->kompania)->name ?? '---' }}</td>
                            <td>{{ $fondi->raised_amount ? number_format($fondi->raised_amount, 2) . ' ' . ($fondi->raised_currency_code ?? 'USD') : '---' }}
                            </td>
                            <td>{{ optional($fondi->funded_at)->format('Y-m-d') ?? '---' }}</td>
                            <td>
                                <a href="{{ route($route . '.show', $fondi->id) }}" class="btn btn-sm btn-info">Show</a>
                                <a href="{{ route($route . '.edit', $fondi->id) }}"
                                    class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route($route . '.destroy', $fondi->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('A je i sigurt?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Nuk ka fonde të regjistruara.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($fondet->hasPages())
            <div class="card-footer bg-light">
                {{ $fondet->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
@endsection
