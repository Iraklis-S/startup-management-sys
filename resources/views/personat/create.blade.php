@php
    $title = 'Personat';
    $route = 'personat';
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
                    </select>
                </div>


                <div class="mb-3 row g-3">
                    <div class="col-md-6">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}">
                        @error('first_name')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}">
                        @error('last_name')
                            <div class="form-text text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Birthplace</label>
                    <input type="text" name="birthplace" class="form-control" value="{{ old('birthplace') }}">
                    @error('birthplace')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Affiliation</label>
                    <input type="text" name="affiliation_name" class="form-control"
                        value="{{ old('affiliation_name') }}">
                    @error('affiliation_name')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <button class="btn btn-primary">Ruaj</button>
                <a href="{{ route($route . '.index') }}" class="btn btn-secondary">Kthehu</a>
            </form>
        </div>
    </div>

    <script>
        $(function() {
            // initialize Select2 (your existing config)
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
                },
                theme: 'bootstrap-5',
                width: '100%'
            });

            // minimal safety: ensure Select2 sets the <select> value on selection
            // (Select2 normally does this automatically; this line is harmless)
            $('#company_id').on('select2:select', function(e) {
                $(this).val(e.params.data.id).trigger('change');
            });
        });
    </script>
@endsection
