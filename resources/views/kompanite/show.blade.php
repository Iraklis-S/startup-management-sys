@php
    $title = 'Kompanitë';
    $route = 'kompanite';
     $latestVerification = $kompania->verifikime()
        ->latest('created_at')
        ->latest('id')
        ->first();

//     dd($latestVerification);

// dd($latestVerification);
@endphp

@extends('layouts.app')

@section('title', $title)

@section('content')
    <h1 class="h3 mb-4">Detajet e {{ $title }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <p><strong>ID:</strong> {{ $kompania->id }}</p>
            <p><strong>Emri / Titulli:</strong> {{ $kompania->name ?? ($kompania->title ?? '---') }}</p>
            <p><strong>Parent Company:</strong> ID : {{ $kompania->parent_id ?? '---' }}
                | Name : {{ $kompania->parent->name ?? '---' }}</p>

            <p>
                <strong>Verification:</strong>
                {{ $latestVerification?->action === 'approved' ? 'Verified' : 'Not verified' }}
            </p>
        </div>
    </div>

    <a href="{{ route($route . '.index') }}" class="btn btn-secondary mt-3">Kthehu</a>
@endsection
