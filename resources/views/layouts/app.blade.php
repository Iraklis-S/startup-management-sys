<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Startup Registry Albania')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet" />

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


    <style>
        :root {
            --sidebar-width: 280px;
            --sidebar-bg: #111827;
            --sidebar-bg-2: #1f2937;
            --sidebar-text: #e5e7eb;
            --sidebar-muted: #9ca3af;
            --sidebar-active: #2563eb;
            --sidebar-hover: rgba(255, 255, 255, 0.06);
            --content-bg: #f3f4f6;
            --card-radius: 1rem;
        }

        body {
            min-height: 100vh;
            background: var(--content-bg);
        }

        .app-shell {
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, var(--sidebar-bg-2) 100%);
            color: var(--sidebar-text);
            position: sticky;
            top: 0;
        }

        .sidebar-brand {
            font-weight: 700;
            font-size: 1.15rem;
            color: #fff;
            text-decoration: none;
            letter-spacing: .2px;
        }

        .sidebar-subtitle {
            color: var(--sidebar-muted);
            font-size: .85rem;
        }

        .sidebar .nav-link {
            color: var(--sidebar-text);
            border-radius: .85rem;
            padding: .75rem .9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: .75rem;
            transition: all .2s ease;
        }

        .sidebar .nav-link:hover {
            background: var(--sidebar-hover);
            color: #fff;
        }

        .sidebar .nav-link.active {
            background: var(--sidebar-active);
            color: #fff;
            box-shadow: 0 10px 25px rgba(37, 99, 235, .25);
        }

        .sidebar .nav-section-title {
            color: var(--sidebar-muted);
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            margin-bottom: .65rem;
        }

        .content-area {
            min-width: 0;
            flex: 1;
        }

        .topbar {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
        }

        .content-wrap {
            padding: 1.5rem;
        }

        .page-card {
            background: #fff;
            border: 1px solid rgba(0, 0, 0, 0.05);
            border-radius: var(--card-radius);
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.05);
            padding: 1.25rem;
        }

        .logout-btn {
            border-radius: .8rem;
        }

        .sidebar-footer {
            border-top: 1px solid rgba(255, 255, 255, 0.08);
        }

        .mobile-brand {
            font-weight: 700;
            letter-spacing: .2px;
        }

        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
        }

        @media (max-width: 991.98px) {
            .content-wrap {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
    <div class="d-flex app-shell">

        {{-- Desktop Sidebar --}}
        <aside class="sidebar d-none d-lg-flex flex-column p-3 shadow-sm">
            <div class="mb-4">
                <a href="{{ route('dashboard') }}" class="sidebar-brand d-inline-block">Startup Registry</a>
                <div class="sidebar-subtitle mt-1">Albania </div>
            </div>

            <div class="nav-section-title">Main Navigation</div>

            <ul class="nav nav-pills flex-column gap-2">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                        href="{{ route('dashboard') }}">
                       
                        <span>Dashboard</span>
                    </a>
                </li>
                    <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kpi.*') ? 'active' : '' }}"
                        href="{{ route('kpi.index') }}">
                
                        <span>KPI</span>
                    </a>
                </li>

                @auth
                    @if(auth()->user()->roli->role_name === 'end_user' && auth()->user()->verification_status === 'verified')
                        <div class="nav-section-title mt-3">End User Actions</div>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('claim-company.*') ? 'active' : '' }}"
                                href="{{ route('claim-company.form') }}">
                            
                                <span>Kërkesa për Kompani</span>
                            </a>
                        </li>
                        <div class="nav-section-title mt-3">Browse Data</div>
                    @else
                        <div class="nav-section-title mt-3">Browse Data</div>
                    @endif
                @endauth

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('kompanite.*') ? 'active' : '' }}"
                        href="{{ route('kompanite.index') }}">
                       
                        <span>Kompanitë</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('personat.*') ? 'active' : '' }}"
                        href="{{ route('personat.index') }}">
                  
                        <span>Personat</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('raundet.*') ? 'active' : '' }}"
                        href="{{ route('raundet.index') }}">
                    
                        <span>Raundet</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('investimet.*') ? 'active' : '' }}"
                        href="{{ route('investimet.index') }}">
                 
                        <span>Investimet</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('fondet.*') ? 'active' : '' }}"
                        href="{{ route('fondet.index') }}">
                    
                        <span>Fondet</span>
                    </a>
                </li>
             <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('zyrat.*') ? 'active' : '' }}"
                        href="{{ route('zyrat.index') }}">
                   
                        <span>Zyrat</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('verifikime.*') ? 'active' : '' }}"
                        href="{{ route('verifikime.queue') }}">
                   
                        <span>Verifikim</span>
                    </a>
                </li>
            </ul>

            <div class="mt-auto pt-4 sidebar-footer">
                @auth
                    <div class="small text-secondary-emphasis text-white-50 mb-2">
                        Logged in
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline-light btn-sm w-100 logout-btn">Logout</button>
                    </form>
                @endauth
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="content-area d-flex flex-column">

            {{-- Mobile Topbar --}}
            <header class="topbar d-lg-none sticky-top">
                <div class="container-fluid py-2 px-3 d-flex align-items-center justify-content-between">
                    <button class="btn btn-dark" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                        ☰
                    </button>

                    <div class="mobile-brand">Startup Registry</div>

                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="btn btn-outline-dark btn-sm">Logout</button>
                        </form>
                    @else
                        <div style="width: 56px;"></div>
                    @endauth
                </div>
            </header>

            {{-- Desktop top spacing / page area --}}
            <main class="content-wrap">
                <div class="page-card">
                    @if (session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif


                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    {{-- Mobile Offcanvas Sidebar --}}
    <div class="offcanvas offcanvas-start text-bg-dark" tabindex="-1" id="mobileSidebar"
        aria-labelledby="mobileSidebarLabel">
        <div class="offcanvas-header border-bottom border-secondary-subtle">
            <div>
                <h5 class="offcanvas-title mb-0" id="mobileSidebarLabel">Startup Registry</h5>
                <small class="text-white-50">Albania</small>
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
        </div>

        <div class="offcanvas-body d-flex flex-column">
            <ul class="nav nav-pills flex-column gap-2">
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('dashboard') ? 'active bg-primary' : '' }}"
                        href="{{ route('dashboard') }}"> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('kompanite.*') ? 'active bg-primary' : '' }}"
                        href="{{ route('kompanite.index') }}"> Kompanitë</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('personat.*') ? 'active bg-primary' : '' }}"
                        href="{{ route('personat.index') }}">Personat</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('raundet.*') ? 'active bg-primary' : '' }}"
                        href="{{ route('raundet.index') }}"> Raundet</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('investimet.*') ? 'active bg-primary' : '' }}"
                        href="{{ route('investimet.index') }}"> Investimet</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('fondet.*') ? 'active bg-primary' : '' }}"
                        href="{{ route('fondet.index') }}"> Fondet</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('kpi.*') ? 'active bg-primary' : '' }}"
                        href="{{ route('kpi.index') }}"> KPI</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white {{ request()->routeIs('verifikime.*') ? 'active bg-primary' : '' }}"
                        href="{{ route('verifikime.queue') }}"> Verifikim</a>
                </li>
            </ul>

            <div class="mt-auto pt-4">
                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-outline-light w-100">Logout</button>
                    </form>
                @endauth
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.searchable-select').select2({
                theme: 'bootstrap-5',
                width: '100%',
                allowClear: true,
                placeholder: function() {
                    return $(this).data('placeholder') || 'Kërkoni...';
                }
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
