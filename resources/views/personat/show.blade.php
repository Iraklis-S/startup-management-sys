@php
    $title = 'Personat';
    $route = 'personat';
@endphp

@extends('layouts.app')

@section('title', $title)

@section('content')
    <h1 class="h3 mb-4">Detajet e {{ $title }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <p><strong>ID:</strong> {{ $personi->id }}</p>
            <p><strong>Kompania:</strong> {{ optional($personi->kompania)->name ?? '---' }}</p>
            <p><strong>Emri:</strong> {{ $personi->first_name ?? '---' }} {{ $personi->last_name ?? '' }}</p>
            <p><strong>Birthplace:</strong> {{ $personi->birthplace ?? '---' }}</p>
            <p><strong>Affiliation:</strong> {{ $personi->affiliation_name ?? '---' }}</p>
        </div>
    </div>

    <a href="{{ route($route . '.index') }}" class="btn btn-secondary mt-3">Kthehu</a>
@endsection
