@php
    $title = 'Fondet';
    $route = 'fondet';
@endphp

@extends('layouts.app')

@section('title', 'Shto ' . $title)

@section('content')
    <h1 class="h3 mb-4">Shto Fond</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route($route . '.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Kompania *</label>
                    <select name="company_id" id="company_id" class="form-select" data-placeholder="Kërkoni..." required>
                      
                    </select>
                    @error('company_id')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Emri i Fondit</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                    @error('name')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Data e Krijimit</label>
                        <input type="date" name="funded_at" class="form-control" value="{{ old('funded_at') }}">
                        @error('funded_at')<div class="form-text text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Shuma e Mbledhur</label>
                        <input type="number" step="0.01" name="raised_amount" class="form-control" value="{{ old('raised_amount') }}">
                        @error('raised_amount')<div class="form-text text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Valuta</label>
                        <input type="text" name="raised_currency_code" class="form-control" value="{{ old('raised_currency_code') }}" placeholder="USD">
                        @error('raised_currency_code')<div class="form-text text-danger">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3 mt-3">
                    <label class="form-label">URL</label>
                    <input type="text" name="source_url" class="form-control" 
                    value="{{ old('source_url') }}">
                    @error('source_url')<div class="form-text text-danger">
                        {{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Përshkrim</label>
                    <textarea name="source_description" class="form-control" rows="3">{{ old('source_description') }}</textarea>
                    @error('source_description')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <button class="btn btn-primary">Ruaj</button>
                <a href="{{ route($route . '.index') }}" class="btn btn-secondary">Kthehu</a>
            </form>
        </div>
    </div>

    <script>
          $('#company_id').select2({
            placeholder: function() {
                return $(this).data('placeholder') || 'Kërko...';
            },
            minimumInputLength: 2,
            ajax: {
                url: '{{ route('api.kompanite.search') }}',
                dataType: 'json',
                delay: 300,
                data: params => ({
                    q: params.term
                }),
                processResults: data => ({
                    results: data.results
                }),
                cache: true
            },
            theme: 'bootstrap-5',
            width: '100%'
        });
    </script>

@endsection
