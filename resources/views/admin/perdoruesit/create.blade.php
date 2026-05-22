@extends('layouts.app')

@section('title', 'Shto përdorues')

@section('content')
    <h1 class="h3 mb-4">Shto përdorues</h1>

    <form method="POST" action="#">
        @csrf
        <div class="mb-3">
            <label class="form-label">Emri</label>
            <input type="text" name="name" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Role ID</label>
            <input type="number" name="role_id" class="form-control">
        </div>
        <button class="btn btn-primary">Ruaj</button>
    </form>
@endsection
