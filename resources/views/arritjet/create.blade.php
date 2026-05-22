@php
    $title = 'Arritjet';
    $route = 'arritjet';
@endphp

@extends('layouts.app')

@section('title', 'Shto ' . $title)

@section('content')
    <h1 class="h3 mb-4">Shto Arritje</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route($route . '.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Kompania *</label>
                    <select name=\"company_id\" class="form-select searchable-select" data-placeholder="Kërkoni..." required>
                        <option value=""></option>
                        @foreach($kompanite as $kompani)
                            <option value="{{ $kompani->id }}" {{ old('company_id') == $kompani->id ? 'selected' : '' }}>{{ $kompani->name }}</option>
                        @endforeach
                    </select>
                    @error('company_id')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Data e Milestone</label>
                    <input type="date" name="milestone_at" class="form-control" value="{{ old('milestone_at') }}">
                    @error('milestone_at')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Kodi i Milestone</label>
                    <input type="text" name="milestone_code" class="form-control" value="{{ old('milestone_code') }}">
                    @error('milestone_code')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">URL Burim</label>
                    <input type="url" name="source_url" class="form-control" value="{{ old('source_url') }}">
                    @error('source_url')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Përshkrim Burim</label>
                    <textarea name="source_description" class="form-control" rows="3">{{ old('source_description') }}</textarea>
                    @error('source_description')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <button class="btn btn-primary">Ruaj</button>
                <a href="{{ route($route . '.index') }}" class="btn btn-secondary">Kthehu</a>
            </form>
        </div>
    </div>
@endsection
