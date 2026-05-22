@php
    $title = 'Kompanitë';
    $route = 'kompanite';
    $items = $kompanite ?? collect();
@endphp

@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ $title }}</h1>
        <a href="{{ route($route . '.create') }}" class="btn btn-primary">Shto</a>
    </div>

    <!-- Search / Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route($route . '.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Emri / Titulli</label>
                    <input type="text" name="name" class="form-control" placeholder="Kërko me emër..." value="{{ request('name') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Kategorië</label>
                    <input type="text" name="category_code" class="form-control" placeholder="Kategorië" value="{{ request('category_code') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Të gjithë</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Lloji</label>
                    <select name="company_type" class="form-select">
                        <option value="">Të gjithë</option>
                        <option value="startup" {{ request('company_type') == 'startup' ? 'selected' : '' }}>Startups</option>
                        <option value="other" {{ request('company_type') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <div class="w-100 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100">Kërko</button>
                        <a href="{{ route($route . '.index') }}" class="btn btn-secondary w-100">Reseto</a>
                    </div>
                </div>
            </form>
        </div>
        @if(method_exists($kompanite, 'links'))
            <div class="card-footer bg-light">
                {{ $kompanite->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0 bg-white">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Emri / Titulli</th>
                        <th style="width: 220px;">Veprime</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ $item->name ?? ($item->title ?? ($item->first_name ?? '---')) }}</td>
                            <td>
                                @if (Route::has($route . '.show'))
                                    <a href="{{ route($route . '.show', $item) }}" class="btn btn-sm btn-info">Show</a>
                                @endif
                                <a href="{{ route($route . '.edit', $item) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route($route . '.destroy', $item) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('A je i sigurt?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Nuk ka të dhëna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
