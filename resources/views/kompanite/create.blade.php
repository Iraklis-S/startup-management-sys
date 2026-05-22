@php
    $title = 'Kompanitë';
    $route = 'kompanite';

@endphp

@extends('layouts.app')

@section('title', 'Shto ' . $title)

@section('content')

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <h1 class="h3 mb-4">Shto {{ $title }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route($route . '.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Emri / Titulli</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                </div>

               
                <div class="mb-3">
                    <label for="parent_id" class="form-label">Parent Company (optional)</label>
                    <select name="parent_id" id="parent_id" class="form-control"></select>
                    <small class="text-muted">Search only if this company is a branch/subsidiary.</small>
                </div>
                <button class="btn btn-primary">Ruaj</button>
                <a href="{{ route($route . '.index') }}" class="btn btn-secondary">Kthehu</a>
            </form>
        </div>
    </div>

    <script>
        $('#parent_id').select2({
            placeholder: 'Search parent company...',
            allowClear: true,
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
