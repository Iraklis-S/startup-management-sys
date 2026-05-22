@php
    $title = 'Edukimet';
    $route = 'edukimet';
    $items = $edukimet ?? collect();
@endphp

@extends('layouts.app')

@section('title', $title)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">{{ $title }}</h1>
        <a href="{{ route($route . '.create') }}" class="btn btn-primary">Shto</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-bordered table-hover mb-0 bg-white">
                <thead>
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Person</th>
                        <th>Degree</th>
                        <th>Institution</th>
                        <th>Graduated At</th>
                        <th style="width: 220px;">Veprime</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ optional($item->personi)->first_name ?? '---' }} {{ optional($item->personi)->last_name ?? '' }}</td>
                            <td>{{ $item->degree_type ?? '---' }}</td>
                            <td>{{ $item->institution ?? '---' }}</td>
                            <td>{{ optional($item->graduated_at)->format('Y-m-d') ?? '---' }}</td>
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
                            <td colspan="6" class="text-center">Nuk ka të dhëna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
