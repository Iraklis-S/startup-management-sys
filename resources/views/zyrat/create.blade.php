@php
    $title = 'Zyrat';
    $route = 'zyrat';
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


                    <label for="company_id" class="form-label">Kompania</label>
                    <select name="company_id" id="company_id" class="form-control"></select>

                    @error('company_id')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Office ID</label>
                    <input type="text" name="office_id" class="form-control" value="{{ old('office_id') }}">
                    @error('office_id')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                    @error('city')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Region</label>
                            <input type="text" name="region" class="form-control" value="{{ old('region') }}">
                            @error('region')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Zip Code</label>
                            <input type="text" name="zip_code" class="form-control" value="{{ old('zip_code') }}">
                            @error('zip_code')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Latitude</label>
                            <input type="number" step="0.000001" name="latitude" class="form-control"
                                value="{{ old('latitude') }}">
                            @error('latitude')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Longitude</label>
                            <input type="number" step="0.000001" name="longitude" class="form-control"
                                value="{{ old('longitude') }}">
                            @error('longitude')
                                <div class="form-text text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-primary">Ruaj</button>
                <a href="{{ route($route . '.index') }}" class="btn btn-secondary">Kthehu</a>
            </form>
        </div>
    </div>

      <script>
        $('#company_id').select2({
            placeholder: 'Kerko kompanine...',
            minimumInputLength: 2,
            ajax: {
                url: '{{ route('api.kompanite.search') }}',
                dataType: 'json',
                delay: 300,
                data: function(params) {
                    return {
                        q: params.term
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.results
                    };
                },
                cache: true
            }
        });
    </script>
@endsection
