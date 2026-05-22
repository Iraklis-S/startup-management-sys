@php
    $title = 'Investimet';
    $route = 'investimet';
@endphp

@extends('layouts.app')

@section('title', 'Përditëso ' . $title)

@section('content')
    <h1 class="h3 mb-4">Përditëso {{ $title }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route($route . '.update', $investim->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Raundi Financiar *</label>
                    <select name="funding_round_id" class="form-select searchable-select" data-placeholder="Kërkoni raund..." required>
                        <option value=""></option>
                        @foreach($raundet as $raund)
                            <option value="{{ $raund->id }}" {{ old('funding_round_id', $investim->funding_round_id) == $raund->id ? 'selected' : '' }}>
                                {{ $raund->kompania->name ?? 'N/A' }} - {{ $raund->funding_round_type ?? 'Round' }}
                            </option>
                        @endforeach
                    </select>
                    @error('funding_round_id')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Kompania që Maron Investim *</label>
                    <select name="funded_company_id" class="form-select searchable-select" data-placeholder="Kërkoni kompani..." required>
                        <option value=""></option>
                        @foreach($kompanite as $kompani)
                            <option value="{{ $kompani->id }}" {{ old('funded_company_id', $investim->funded_company_id) == $kompani->id ? 'selected' : '' }}>{{ $kompani->name }}</option>
                        @endforeach
                    </select>
                    @error('funded_company_id')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Investitori *</label>
                    <select name="investor_company_id" class="form-select searchable-select" data-placeholder="Kërkoni investitor..." required>
                        <option value=""></option>
                        @foreach($kompanite as $kompani)
                            <option value="{{ $kompani->id }}" {{ old('investor_company_id', $investim->investor_company_id) == $kompani->id ? 'selected' : '' }}>{{ $kompani->name }}</option>
                        @endforeach
                    </select>
                    @error('investor_company_id')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">Përditëso Investimin</button>
                    <a href="{{ route($route . '.index') }}" class="btn btn-secondary">Kthehu</a>
                </div>
            </form>
        </div>
    </div>
