@php
    $title = 'Edukimet';
    $route = 'edukimet';
@endphp

@extends('layouts.app')

@section('title', 'Edit ' . $title)

@section('content')
<h1 class="h3 mb-4">Edit {{ $title }}</h1>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route($route . '.update', $edukimi->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Person</label>
                <select name=\"company_id\" class="form-select">
                    <option value="">Zgjidh person</option>
                    @foreach($personat as $person)
                        <option value="{{ $person->company_id }}" {{ old('company_id', $edukimi->company_id) == $person->company_id ? 'selected' : '' }}>{{ $person->first_name }} {{ $person->last_name }}</option>
                    @endforeach
                </select>
                @error('company_id')<div class="form-text text-danger">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Degree Type</label>
                <input type="text" name="degree_type" class="form-control" value="{{ old('degree_type', $edukimi->degree_type) }}">
                @error('degree_type')<div class="form-text text-danger">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" value="{{ old('subject', $edukimi->subject) }}">
                @error('subject')<div class="form-text text-danger">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Institution</label>
                <input type="text" name="institution" class="form-control" value="{{ old('institution', $edukimi->institution) }}">
                @error('institution')<div class="form-text text-danger">{{ $message }}</div>@enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Graduated At</label>
                <input type="date" name="graduated_at" class="form-control" value="{{ old('graduated_at', optional($edukimi->graduated_at)->format('Y-m-d')) }}">
                @error('graduated_at')<div class="form-text text-danger">{{ $message }}</div>@enderror
            </div>

            <button class="btn btn-primary">Përditëso</button>
            <a href="{{ route($route . '.index') }}" class="btn btn-secondary">Kthehu</a>
        </form>
    </div>
</div>
@endsection