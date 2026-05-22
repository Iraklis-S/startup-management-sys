@extends('layouts.app')

@section('title', 'Edit përdorues')

@section('content')
    <h1 class="h3 mb-4">Edit përdorues</h1>

    <form method="POST" action="#">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Emri</label>
            <input type="text" name="name" class="form-control" value="{{ $perdoruesit->name ?? '' }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ $perdoruesit->email ?? '' }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Role ID</label>
            <input type="number" name="role_id" class="form-control" value="{{ $perdoruesit->role_id ?? '' }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Aktiv</label>
            <select name="is_active" class="form-select">
                <option value="1">Po</option>
                <option value="0">Jo</option>
            </select>
        </div>
        <button class="btn btn-primary">Përditëso</button>
    </form>
@endsection
