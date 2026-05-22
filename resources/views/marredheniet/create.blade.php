@php
    $title = 'Marredheniet';
    $route = 'marredheniet';
@endphp

@extends('layouts.app')

@section('title', 'Shto ' . $title)

@section('content')
    <h1 class="h3 mb-4">Shto {{ $title }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route($route . '.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Person</label>
                    <select name="person_id" class="form-select">
                        <option value="">Zgjidh person</option>
                        @foreach($personat as $person)
                            <option value="{{ $person->company_id }}" {{ old('person_id') == $person->company_id ? 'selected' : '' }}>{{ $person->first_name }} {{ $person->last_name }}</option>
                        @endforeach
                    </select>
                    @error('person_id')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Kompania</label>
                    <select name=\"company_id\" class="form-select">
                        <option value="">Zgjidh kompani</option>
                        @foreach($kompanite as $kompani)
                            <option value="{{ $kompani->id }}" {{ old('company_id') == $kompani->id ? 'selected' : '' }}>{{ $kompani->name }}</option>
                        @endforeach
                    </select>
                    @error('company_id')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Start At</label>
                            <input type="date" name="start_at" class="form-control" value="{{ old('start_at') }}">
                            @error('start_at')<div class="form-text text-danger">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">End At</label>
                            <input type="date" name="end_at" class="form-control" value="{{ old('end_at') }}">
                            @error('end_at')<div class="form-text text-danger">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3 form-check">
                    <input type="hidden" name="is_past" value="0">
                    <input type="checkbox" name="is_past" value="1" class="form-check-input" id="is_past" {{ old('is_past') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_past">Historike</label>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sequence</label>
                    <input type="number" name="sequence" class="form-control" value="{{ old('sequence') }}">
                    @error('sequence')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                    @error('title')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <button class="btn btn-primary">Ruaj</button>
                <a href="{{ route($route . '.index') }}" class="btn btn-secondary">Kthehu</a>
            </form>
        </div>
    </div>
@endsection
