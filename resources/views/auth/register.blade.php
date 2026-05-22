<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #0b1223;
            color: #e5e7eb;
            min-height: 100vh;
            font-family: ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding-top: 2rem;
            padding-bottom: 2rem;
        }

        .card {
            background: #111827;
            border: 1px solid rgba(148, 163, 184, 0.18);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(148, 163, 184, 0.12);
            color: #f8fafc;
        }

        .card-body {
            color: #e5e7eb;
        }

        .form-label {
            color: #cbd5e1;
        }

        .form-control {
            background: #1f2937;
            border: 1px solid rgba(148, 163, 184, 0.18);
            color: #e5e7eb;
        }

        .form-control:focus {
            background: #1f2937;
            color: #e5e7eb;
            border-color: #2563eb;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.25);
        }

        .btn-primary {
            background: #2563eb;
            border-color: #2563eb;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
        }

        a {
            color: #93c5fd;
        }

        a:hover {
            color: #bfdbfe;
        }

        .alert-danger {
            background: rgba(220, 38, 38, 0.1);
            border-color: rgba(220, 38, 38, 0.3);
            color: #fde2e0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row w-100 justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4 class="mb-0">Regjistrim në sistem</h4>
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Emri</label>
                                        <input type="text" name="first_name" class="form-control @error('first_name') is-invalid @enderror"
                                            placeholder="Emri" value="{{ old('first_name') }}" required>
                                        @error('first_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Mbiemri</label>
                                        <input type="text" name="last_name" class="form-control @error('last_name') is-invalid @enderror"
                                            placeholder="Mbiemri" value="{{ old('last_name') }}" required>
                                        @error('last_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Emri i Plotë (për shfaqje)</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Emri i plotë" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                    placeholder="email@domain.com" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                    placeholder="Password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Konfirmo Password</label>
                                <input type="password" name="password_confirmation" class="form-control"
                                    placeholder="Konfirmo password" required>
                            </div>

                            <button class="btn btn-primary w-100 mb-3">Regjistrohu</button>

                            <p class="text-center mb-0">
                                Ke një llogari? <a href="{{ route('login') }}">Hyr në sistem</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
