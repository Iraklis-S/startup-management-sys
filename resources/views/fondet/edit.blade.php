@php
    $title = 'Fondet';
    $route = 'fondet';
@endphp

@extends('layouts.app')

@section('title', 'Përditëso ' . $title)

@section('content')
    <h1 class="h3 mb-4">Përditëso Fond</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route($route . '.update', $fondi->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Kompania *</label>
                    <select name=\"company_id\" class="form-select searchable-select" data-placeholder="Kërkoni..." required>
                        <option value=""></option>
                        @foreach($kompanite as $kompani)
                            <option value="{{ $kompani->id }}" {{ old('company_id', $fondi->company_id) == $kompani->id ? 'selected' : '' }}>{{ $kompani->name }}</option>
                        @endforeach
                    </select>
                    @error('company_id')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Emri i Fondit</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $fondi->name) }}">
                    @error('name')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Data e Fondimit</label>
                        <input type="date" name="funded_at" class="form-control" value="{{ old('funded_at', optional($fondi->funded_at)->format('Y-m-d')) }}">
                        @error('funded_at')<div class="form-text text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Shuma e Mbledhur</label>
                        <input type="number" step="0.01" name="raised_amount" class="form-control" value="{{ old('raised_amount', $fondi->raised_amount) }}">
                        @error('raised_amount')<div class="form-text text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Valuta</label>
                        <input type="text" name="raised_currency_code" class="form-control" value="{{ old('raised_currency_code', $fondi->raised_currency_code) }}" placeholder="USD">
                        @error('raised_currency_code')<div class="form-text text-danger">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label">URL Burim</label>
                    <input type="url" name="source_url" class="form-control" value="{{ old('source_url', $fondi->source_url) }}">
                    @error('source_url')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Përshkrim</label>
                    <textarea name="source_description" class="form-control" rows="3">{{ old('source_description', $fondi->source_description) }}</textarea>
                    @error('source_description')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <button class="btn btn-primary">Përditëso</button>
                <a href="{{ route($route . '.index') }}" class="btn btn-secondary">Kthehu</a>
            </form>
        </div>
    </div>
@endsection
