@extends('layouts.app')

@section('title', 'Përdoruesit')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Menaxhimi i Përdoruesve</h1>
        <a href="{{ route('admin.perdoruesit.create') }}" class="btn btn-primary">Shto përdorues</a>
    </div>

    <table class="table table-bordered bg-white">
        <thead>
            <tr>
                <th>ID</th>
                <th>Emri</th>
                <th>Email</th>
                <th>Roli</th>
                <th>Aktiv</th>
                <th>Veprime</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($perdoruesit ?? []) as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->roli->role_name ?? '-' }}</td>
                    <td>{{ $user->is_active ? 'Po' : 'Jo' }}</td>
                    <td>
                        <a href="{{ route('admin.perdoruesit.edit', $user->id) }}" class="btn btn-sm btn-warning">Edit</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Nuk ka përdorues.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
@endsection
