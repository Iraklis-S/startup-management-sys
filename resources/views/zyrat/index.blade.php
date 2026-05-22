@php
    $title = 'Zyrat';
    $route = 'zyrat';

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
                        <th>Kompania</th>
                        <th>City</th>
                        <th>Region</th>
                        <th>Office ID</th>
                        <th style="width: 220px;">Veprime</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($zyrat as $zyra)
                        <tr>
                            <td>{{ $zyra->id }}</td>
                            <td>{{ optional($zyra->kompania)->name ?? '---' }}</td>
                            <td>{{ $zyra->city ?? '---' }}</td>
                            <td>{{ $zyra->region ?? '---' }}</td>
                            <td>{{ $zyra->id ?? '---' }}</td>
                            <td>

                                <a href="{{ route($route . '.edit', ['zyra' => $zyra]) }}"
                                    class="btn btn-sm btn-warning">Edit</a>
                                <form action="{{ route($route . '.destroy', ['zyra' => $zyra]) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger"
                                        onclick="return confirm('A je i sigurt?')">Delete</button>
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
          <div class="card-footer bg-light">
                {{ $zyrat->links('pagination::bootstrap-5') }}
            </div>
    </div>
@endsection
