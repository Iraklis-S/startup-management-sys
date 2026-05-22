@php
    $title = 'Blerjet';
    $route = 'blerjet';
@endphp

@extends('layouts.app')

@section('title', 'Përditëso ' . $title)

@section('content')
    <h1 class="h3 mb-4">Përditëso Blerje</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route($route . '.update', $blerjet->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Kompania Blerëse *</label>
                    <select name="acquiring_company_id" class="form-select searchable-select" data-placeholder="Kërkoni..." required>
                        <option value=""></option>
                        @foreach($kompanite as $kompani)
                            <option value="{{ $kompani->id }}" {{ old('acquiring_company_id', $blerjet->acquiring_company_id) == $kompani->id ? 'selected' : '' }}>{{ $kompani->name }}</option>
                        @endforeach
                    </select>
                    @error('acquiring_company_id')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Kompania e Blere *</label>
                    <select name="acquired_company_id" class="form-select searchable-select" data-placeholder="Kërkoni..." required>
                        <option value=""></option>
                        @foreach($kompanite as $kompani)
                            <option value="{{ $kompani->id }}" {{ old('acquired_company_id', $blerjet->acquired_company_id) == $kompani->id ? 'selected' : '' }}>{{ $kompani->name }}</option>
                        @endforeach
                    </select>
                    @error('acquired_company_id')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Çmimi</label>
                            <input type="number" step="0.01" name="price_amount" class="form-control" value="{{ old('price_amount', $blerjet->price_amount) }}">
                            @error('price_amount')<div class="form-text text-danger">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Valuta</label>
                            <input type="text" name="price_currency_code" class="form-control" value="{{ old('price_currency_code', $blerjet->price_currency_code) }}" placeholder="USD, EUR">
                            @error('price_currency_code')<div class="form-text text-danger">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Data e Blerjës</label>
                    <input type="date" name="acquired_at" class="form-control" value="{{ old('acquired_at', $blerjet->acquired_at?->format('Y-m-d')) }}">
                    @error('acquired_at')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">URL Burim</label>
                    <input type="url" name="source_url" class="form-control" value="{{ old('source_url', $blerjet->source_url) }}">
                    @error('source_url')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Përshkrim Burim</label>
                    <textarea name="source_description" class="form-control" rows="3">{{ old('source_description', $blerjet->source_description) }}</textarea>
                    @error('source_description')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Përditëso</button>
                    <a href="{{ route($route . '.index') }}" class="btn btn-secondary">Kthehu</a>
                </div>
            </form>
        </div>
    </div>
@endsection
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Price Amount</label>
                        <input type="number" step="0.01" name="price_amount" class="form-control" value="{{ old('price_amount', $blerjet->price_amount) }}">
                        @error('price_amount')<div class="form-text text-danger">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Price Currency</label>
                        <input type="text" name="price_currency_code" class="form-control" value="{{ old('price_currency_code', $blerjet->price_currency_code) }}">
                        @error('price_currency_code')<div class="form-text text-danger">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Acquired At</label>
                <input type="date" name="acquired_at" class="form-control" value="{{ old('acquired_at', optional($blerjet->acquired_at)->format('Y-m-d')) }}">
                @error('acquired_at')<div class="form-text text-danger">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Source URL</label>
                <input type="url" name="source_url" class="form-control" value="{{ old('source_url', $blerjet->source_url) }}">
                @error('source_url')<div class="form-text text-danger">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Source Description</label>
                <textarea name="source_description" class="form-control" rows="3">{{ old('source_description', $blerjet->source_description) }}</textarea>
                @error('source_description')<div class="form-text text-danger">{{ $message }}</div>@enderror
            </div>

            <button class="btn btn-primary">Përditëso</button>
            <a href="{{ route($route . '.index') }}" class="btn btn-secondary">Kthehu</a>
        </form>
    </div>
</div>
@endsection