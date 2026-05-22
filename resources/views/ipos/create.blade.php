@php
    $title = 'Ipos';
    $route = 'ipos';
@endphp

@extends('layouts.app')

@section('title', 'Shto ' . $title)

@section('content')
    <h1 class="h3 mb-4">Shto IPO</h1>

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

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Valuation Amount</label>
                        <input type="number" step="0.01" name="valuation_amount" class="form-control" value="{{ old('valuation_amount') }}">
                        @error('valuation_amount')<div class="form-text text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Valuation Currency</label>
                        <input type="text" name="valuation_currency_code" class="form-control" value="{{ old('valuation_currency_code') }}" placeholder="USD">
                        @error('valuation_currency_code')<div class="form-text text-danger">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <label class="form-label">Raised Amount</label>
                        <input type="number" step="0.01" name="raised_amount" class="form-control" value="{{ old('raised_amount') }}">
                        @error('raised_amount')<div class="form-text text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Raised Currency</label>
                        <input type="text" name="raised_currency_code" class="form-control" value="{{ old('raised_currency_code') }}" placeholder="USD">
                        @error('raised_currency_code')<div class="form-text text-danger">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label">Data e Publikimit</label>
                    <input type="date" name="public_at" class="form-control" value="{{ old('public_at') }}">
                    @error('public_at')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Stock Symbol</label>
                    <input type="text" name="stock_symbol" class="form-control" value="{{ old('stock_symbol') }}">
                    @error('stock_symbol')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Source URL</label>
                    <input type="url" name="source_url" class="form-control" value="{{ old('source_url') }}">
                    @error('source_url')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <button class="btn btn-primary">Ruaj</button>
                <a href="{{ route($route . '.index') }}" class="btn btn-secondary">Kthehu</a>
            </form>
        </div>
    </div>
@endsection