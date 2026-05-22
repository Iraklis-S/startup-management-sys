<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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

        .card-footer {
            background: transparent;
            border-top: 1px solid rgba(148, 163, 184, 0.12);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row w-100 justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h4 class="mb-0">Hyrje në sistem</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('login.post') }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" placeholder="email@domain.com"
                                    value="{{ old('email') }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" placeholder="password" class="form-control" required>
                            </div>
                            <button class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p class="mb-0">Nuk ke llogari? <a href="{{ route('register.form') }}">Regjistrohu këtu</a></p>
