@php
    $title = 'Personat';
    $route = 'personat';
@endphp

@extends('layouts.app')

@section('title', 'Edit ' . $title)

@section('content')
    <h1 class="h3 mb-4">Edit {{ $title }}</h1>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route($route . '.update',['personi'=>$personi]) }}">
                @csrf
                @method('PUT')

                  <div class="mb-3">
                    <label for="company_id" class="form-label">Kompania</label>
                    <select name="company_id" id="company_id" class="form-control">
                        @if($personi->company_id)
                            <option value="{{ $personi->company_id }}">{{ optional($personi->kompania)->name ?? 'Kompania #' . $personi->company_id }} (#{{ $personi->company_id }})</option>
                        @endif
                    </select>

                    @error('company_id')
                        <div class="form-text text-danger">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 row g-3">
                    <div class="col-md-6">
                        <label class="form-label">First Name</label>
                        <input type="text" name="first_name" class="form-control" value="{{ old('first_name', $personi->first_name) }}">
                        @error('first_name')<div class="form-text text-danger">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Last Name</label>
                        <input type="text" name="last_name" class="form-control" value="{{ old('last_name', $personi->last_name) }}">
                        @error('last_name')<div class="form-text text-danger">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Birthplace</label>
                    <input type="text" name="birthplace" class="form-control" value="{{ old('birthplace', $personi->birthplace) }}">
                    @error('birthplace')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Affiliation</label>
                    <input type="text" name="affiliation_name" class="form-control" value="{{ old('affiliation_name', $personi->affiliation_name) }}">
                    @error('affiliation_name')<div class="form-text text-danger">{{ $message }}</div>@enderror
                </div>

                <button class="btn btn-primary">Përditëso</button>
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

        // Ensure current company is selected on load
        @if($personi->company_id)
            $('#company_id').val('{{ $personi->company_id }}').trigger('change');
        @endif
    </script>
@endsection
