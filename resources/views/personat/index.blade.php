@php
    $title = 'Personat';
    $route = 'personat';
    $items = $personat ?? collect();
@endphp

@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ $title }}</h1>
        <a href="{{ route($route . '.create') }}" class="btn btn-primary">Shto</a>
    </div>

    <!-- Quick search -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route($route . '.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Kërko person</label>
                    <input type="text" name="q" class="form-control" placeholder="Emri, Mbiemri ose Affilimi" value="{{ request('q') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <div class="w-100 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Kërko</button>
                        <a href="{{ route($route . '.index') }}" class="btn btn-secondary w-100">Reseto</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0 bg-white">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Emri</th>
                        <th>Affiliation</th>
                        <th style="width: 220px;">Veprime</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ trim(($item->first_name ?? '') . ' ' . ($item->last_name ?? '')) ?: '---' }}</td>
                            <td>{{ $item->affiliation_name ?? '---' }}</td>
                            <td>
                                @if (Route::has($route . '.show'))
                                    <a href="{{ route($route . '.show', $item->id) }}" class="btn btn-sm btn-info">Show</a>
                                @endif
                                <a href="{{ route($route . '.edit', $item->id) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route($route . '.destroy', $item->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('A je i sigurt?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Nuk ka të dhëna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        
        </div>
            {{ $items->links() }}
    </div>
@endsection
