@php
    $title = 'Kompanitë';
    $route = 'kompanite';
@endphp

@extends('layouts.app')

@section('title', 'Edit ' . $title)

@section('content')
    <h1 class="h3 mb-4">Edit {{ $title }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route($route . '.update', ['kompania' => $kompania]) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Emri / Titulli</label>
                    <input type="text" name="name" class="form-control"
                        value="{{ old('name', $kompania->name ?? ($kompania->title ?? '')) }}">
                </div>

                <div class="mb-3">
                    <label class="form-label">Përshkrim</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description', $kompania->description ?? '') }}</textarea>
                </div>

                <button class="btn btn-primary">Përditëso</button>
                <a href="{{ route($route . '.index') }}" class="btn btn-secondary">Kthehu</a>
            </form>
        </div>
    </div>
@endsection
