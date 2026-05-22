@extends('layouts.app')

@section('title', 'Verifikime')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 mb-0">Verifikime</h1>
            <p class="text-muted">Shfaq të gjitha regjistrimet që presin miratim ose rishikim.</p>
        </div>
    </div>

    <div class="card">
        <div class="card-header p-0 border-bottom-0">
            <div class="p-3">
                <form method="GET" class="row g-2">
                    <div class="col-auto">
                        <select name="filter_type" class="form-select form-select-sm">
                            <option value="">Të gjitha llojet</option>
                            <option value="user" {{ request('filter_type') == 'user' ? 'selected' : '' }}>Përdorues</option>
                            <option value="personi" {{ request('filter_type') == 'personi' ? 'selected' : '' }}>Persona</option>
                            <option value="kompania" {{ request('filter_type') == 'kompania' ? 'selected' : '' }}>Kompanitë</option>
                            <option value="raundi" {{ request('filter_type') == 'raundi' ? 'selected' : '' }}>Raundet</option>
                            <option value="investim" {{ request('filter_type') == 'investim' ? 'selected' : '' }}>Investimet</option>
                            <option value="fond" {{ request('filter_type') == 'fond' ? 'selected' : '' }}>Fondet</option>
                            <option value="blerja" {{ request('filter_type') == 'blerja' ? 'selected' : '' }}>Blerjet</option>
                            <option value="ipo" {{ request('filter_type') == 'ipo' ? 'selected' : '' }}>IPO</option>
                            <option value="arritja" {{ request('filter_type') == 'arritja' ? 'selected' : '' }}>Arritjet</option>
                            <option value="marredhenia" {{ request('filter_type') == 'marredhenia' ? 'selected' : '' }}>Marëdhëniet</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <select name="status" class="form-select form-select-sm">
                            <option value="pending" {{ request('status', 'pending') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="verified" {{ request('status') === 'verified' ? 'selected' : '' }}>Verified</option>
                            <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>Të gjitha statuset</option>
                        </select>
                    </div>
                    <div class="col">
                        <input type="search" name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Kërko sipas emrit...">
                    </div>
                    <input type="hidden" name="active_tab" id="active_tab" value="{{ request('active_tab', request('filter_type') ?: 'kompanite') }}">
                    <div class="col-auto">
                        <button type="submit" class="btn btn-sm btn-primary">Kërko</button>
                        <a href="{{ route('verifikime.queue') }}" class="btn btn-sm btn-outline-secondary ms-2">Pastro</a>
                    </div>
                </form>
            </div>
            <ul class="nav nav-tabs card-header-tabs" id="verificationTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="false">
                        Përdorues <span class="badge bg-secondary">{{ method_exists($users_pending, 'total') ? $users_pending->total() : $users_pending->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="personat-tab" data-bs-toggle="tab" data-bs-target="#personat" type="button" role="tab" aria-controls="personat" aria-selected="false">
                        Persona <span class="badge bg-secondary">{{ method_exists($personat_pending, 'total') ? $personat_pending->total() : $personat_pending->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="kompanite-tab" data-bs-toggle="tab" data-bs-target="#kompanite" type="button" role="tab" aria-controls="kompanite" aria-selected="true">
                        Kompanitë <span class="badge bg-secondary">{{ method_exists($kompanite_pending, 'total') ? $kompanite_pending->total() : $kompanite_pending->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="raundet-tab" data-bs-toggle="tab" data-bs-target="#raundet" type="button" role="tab" aria-controls="raundet" aria-selected="false">
                        Raundet <span class="badge bg-secondary">{{ method_exists($raundet_pending, 'total') ? $raundet_pending->total() : $raundet_pending->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="investimet-tab" data-bs-toggle="tab" data-bs-target="#investimet" type="button" role="tab" aria-controls="investimet" aria-selected="false">
                        Investimet <span class="badge bg-secondary">{{ method_exists($investimet_pending, 'total') ? $investimet_pending->total() : $investimet_pending->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="fondet-tab" data-bs-toggle="tab" data-bs-target="#fondet" type="button" role="tab" aria-controls="fondet" aria-selected="false">
                        Fondet <span class="badge bg-secondary">{{ method_exists($fondet_pending, 'total') ? $fondet_pending->total() : $fondet_pending->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="blerjet-tab" data-bs-toggle="tab" data-bs-target="#blerjet" type="button" role="tab" aria-controls="blerjet" aria-selected="false">
                        Blerjet <span class="badge bg-secondary">{{ method_exists($blerjet_pending, 'total') ? $blerjet_pending->total() : $blerjet_pending->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="ipos-tab" data-bs-toggle="tab" data-bs-target="#ipos" type="button" role="tab" aria-controls="ipos" aria-selected="false">
                        IPO-t <span class="badge bg-secondary">{{ method_exists($ipos_pending, 'total') ? $ipos_pending->total() : $ipos_pending->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="arritjet-tab" data-bs-toggle="tab" data-bs-target="#arritjet" type="button" role="tab" aria-controls="arritjet" aria-selected="false">
                        Arritjet <span class="badge bg-secondary">{{ method_exists($arritjet_pending, 'total') ? $arritjet_pending->total() : $arritjet_pending->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="marredheniet-tab" data-bs-toggle="tab" data-bs-target="#marredheniet" type="button" role="tab" aria-controls="marredheniet" aria-selected="false">
                        Marëdhënie <span class="badge bg-secondary">{{ method_exists($marredheniet_pending, 'total') ? $marredheniet_pending->total() : $marredheniet_pending->count() }}</span>
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="tab-content" id="verificationTabsContent">
                <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
                    @include('verifikues.partials.table', [
                        'records' => $users_pending,
                        'headers' => ['ID', 'Emri', 'Email', 'Status', 'Krijuar', 'Verifikues', 'Verif. Koha'],
                        'rows' => function($record) {
                            $verifier = $record->verified_by ? (\App\Models\User::find($record->verified_by)->name ?? $record->verified_by) : '—';
                            return [
                                $record->id,
                                $record->name,
                                $record->email,
                                ucfirst($record->verification_status),
                                optional($record->created_at)->format('Y-m-d'),
                                $verifier,
                                $record->verified_at ? optional($record->verified_at)->format('Y-m-d H:i') : '—',
                            ];
                        },
                        'type' => 'user',
                    ])
                </div>

                <div class="tab-pane fade" id="personat" role="tabpanel" aria-labelledby="personat-tab">
                    @include('verifikues.partials.table', [
                        'records' => $personat_pending,
                        'headers' => ['ID', 'Emri', 'Mbiemri', 'Status', 'Krijuar', 'Verifikues', 'Verif. Koha'],
                        'rows' => function($record) {
                            $verifier = $record->verified_by ? (\App\Models\User::find($record->verified_by)->name ?? $record->verified_by) : '—';
                            return [
                                $record->id,
                                $record->first_name,
                                $record->last_name,
                                ucfirst($record->verification_status),
                                optional($record->created_at)->format('Y-m-d'),
                                $verifier,
                                $record->verified_at ? optional($record->verified_at)->format('Y-m-d H:i') : '—',
                            ];
                        },
                        'type' => 'personi',
                    ])
                </div>

                <div class="tab-pane fade show active" id="kompanite" role="tabpanel" aria-labelledby="kompanite-tab">
                    @include('verifikues.partials.table', [
                        'records' => $kompanite_pending,
                        'headers' => ['ID', 'Emri', 'Kategori', 'Status', 'Krijuar', 'Verifikues', 'Verif. Koha'],
                        'rows' => function($record) {
                            $verifier = $record->verified_by ? (\App\Models\User::find($record->verified_by)->name ?? $record->verified_by) : '—';
                            return [
                                $record->id,
                                $record->name,
                                $record->category_code,
                                ucfirst($record->verification_status),
                                optional($record->created_at)->format('Y-m-d'),
                                $verifier,
                                $record->verified_at ? optional($record->verified_at)->format('Y-m-d H:i') : '—',
                            ];
                        },
                        'type' => 'kompania',
                    ])
                </div>

                <div class="tab-pane fade" id="raundet" role="tabpanel" aria-labelledby="raundet-tab">
                    @include('verifikues.partials.table', [
                        'records' => $raundet_pending,
                        'headers' => ['ID', 'Kompania', 'Raundi', 'Shuma (USD)', 'Status', 'Krijuar', 'Verifikues', 'Verif. Koha'],
                        'rows' => function($record) {
                            $verifier = $record->verified_by ? (\App\Models\User::find($record->verified_by)->name ?? $record->verified_by) : '—';
                            return [
                                $record->id,
                                optional($record->kompania)->name ?? '—',
                                $record->funding_round_type ?: $record->funding_round_code,
                                optional($record->raised_amount_usd) ? number_format($record->raised_amount_usd, 2) : '—',
                                ucfirst($record->verification_status),
                                optional($record->created_at)->format('Y-m-d'),
                                $verifier,
                                $record->verified_at ? optional($record->verified_at)->format('Y-m-d H:i') : '—',
                            ];
                        },
                        'type' => 'raundi',
                    ])
                </div>

                <div class="tab-pane fade" id="investimet" role="tabpanel" aria-labelledby="investimet-tab">
                    @include('verifikues.partials.table', [
                        'records' => $investimet_pending,
                        'headers' => ['ID', 'Investitor', 'Investuar', 'Raundi', 'Status', 'Krijuar', 'Verifikues', 'Verif. Koha'],
                        'rows' => function($record) {
                            $verifier = $record->verified_by ? (\App\Models\User::find($record->verified_by)->name ?? $record->verified_by) : '—';
                            return [
                                $record->id,
                                optional($record->investorKompania)->name ?? '—',
                                optional($record->fundedKompania)->name ?? '—',
                                $record->funding_round_id,
                                ucfirst($record->verification_status),
                                optional($record->created_at)->format('Y-m-d'),
                                $verifier,
                                $record->verified_at ? optional($record->verified_at)->format('Y-m-d H:i') : '—',
                            ];
                        },
                        'type' => 'investim',
                    ])
                </div>

                <div class="tab-pane fade" id="fondet" role="tabpanel" aria-labelledby="fondet-tab">
                    @include('verifikues.partials.table', [
                        'records' => $fondet_pending,
                        'headers' => ['ID', 'Kompania', 'Fond', 'Shuma', 'Status', 'Krijuar', 'Verifikues', 'Verif. Koha'],
                        'rows' => function($record) {
                            $verifier = $record->verified_by ? (\App\Models\User::find($record->verified_by)->name ?? $record->verified_by) : '—';
                            return [
                                $record->id,
                                optional($record->kompania)->name ?? '—',
                                $record->name,
                                optional($record->raised_amount) ? number_format($record->raised_amount, 2) : '—',
                                ucfirst($record->verification_status),
                                optional($record->created_at)->format('Y-m-d'),
                                $verifier,
                                $record->verified_at ? optional($record->verified_at)->format('Y-m-d H:i') : '—',
                            ];
                        },
                        'type' => 'fond',
                    ])
                </div>

                <div class="tab-pane fade" id="blerjet" role="tabpanel" aria-labelledby="blerjet-tab">
                    @include('verifikues.partials.table', [
                        'records' => $blerjet_pending,
                        'headers' => ['ID', 'Blerësi', 'Objekti i blerë', 'Çmimi', 'Status', 'Krijuar', 'Verifikues', 'Verif. Koha'],
                        'rows' => function($record) {
                            $verifier = $record->verified_by ? (\App\Models\User::find($record->verified_by)->name ?? $record->verified_by) : '—';
                            return [
                                $record->id,
                                optional($record->acquiringKompania)->name ?? '—',
                                optional($record->acquiredKompania)->name ?? '—',
                                optional($record->price_amount) ? number_format($record->price_amount, 2) : '—',
                                ucfirst($record->verification_status),
                                optional($record->created_at)->format('Y-m-d'),
                                $verifier,
                                $record->verified_at ? optional($record->verified_at)->format('Y-m-d H:i') : '—',
                            ];
                        },
                        'type' => 'blerja',
                    ])
                </div>

                <div class="tab-pane fade" id="ipos" role="tabpanel" aria-labelledby="ipos-tab">
                    @include('verifikues.partials.table', [
                        'records' => $ipos_pending,
                        'headers' => ['ID', 'Kompania', 'Simbol', 'Shuma', 'Status', 'Krijuar', 'Verifikues', 'Verif. Koha'],
                        'rows' => function($record) {
                            $verifier = $record->verified_by ? (\App\Models\User::find($record->verified_by)->name ?? $record->verified_by) : '—';
                            return [
                                $record->id,
                                optional($record->kompania)->name ?? '—',
                                $record->stock_symbol,
                                optional($record->raised_amount) ? number_format($record->raised_amount, 2) : '—',
                                ucfirst($record->verification_status),
                                optional($record->created_at)->format('Y-m-d'),
                                $verifier,
                                $record->verified_at ? optional($record->verified_at)->format('Y-m-d H:i') : '—',
                            ];
                        },
                        'type' => 'ipo',
                    ])
                </div>

                <div class="tab-pane fade" id="arritjet" role="tabpanel" aria-labelledby="arritjet-tab">
                    @include('verifikues.partials.table', [
                        'records' => $arritjet_pending,
                        'headers' => ['ID', 'Kompania', 'Milestone', 'Data', 'Status', 'Krijuar', 'Verifikues', 'Verif. Koha'],
                        'rows' => function($record) {
                            $verifier = $record->verified_by ? (\App\Models\User::find($record->verified_by)->name ?? $record->verified_by) : '—';
                            return [
                                $record->id,
                                optional($record->kompania)->name ?? '—',
                                $record->milestone_code,
                                optional($record->milestone_at)->format('Y-m-d'),
                                ucfirst($record->verification_status),
                                optional($record->created_at)->format('Y-m-d'),
                                $verifier,
                                $record->verified_at ? optional($record->verified_at)->format('Y-m-d H:i') : '—',
                            ];
                        },
                        'type' => 'arritja',
                    ])
                </div>

                <div class="tab-pane fade" id="marredheniet" role="tabpanel" aria-labelledby="marredheniet-tab">
                    @include('verifikues.partials.table', [
                        'records' => $marredheniet_pending,
                        'headers' => ['ID', 'Personi', 'Kompania', 'Titulli', 'Status', 'Krijuar', 'Verifikues', 'Verif. Koha'],
                        'rows' => function($record) {
                            $verifier = $record->verified_by ? (\App\Models\User::find($record->verified_by)->name ?? $record->verified_by) : '—';
                            return [
                                $record->id,
                                optional($record->personi)->name ?? '—',
                                optional($record->kompania)->name ?? '—',
                                $record->title,
                                ucfirst($record->verification_status),
                                optional($record->created_at)->format('Y-m-d'),
                                $verifier,
                                $record->verified_at ? optional($record->verified_at)->format('Y-m-d H:i') : '—',
                            ];
                        },
                        'type' => 'marredhenia',
                    ])
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var activeTab = document.getElementById('active_tab');
            if (!activeTab) {
                return;
            }
            var activeValue = activeTab.value || 'kompanite';
            var tabButton = document.querySelector('#verificationTabs button[data-bs-target="#' + activeValue + '"]');
            if (tabButton && typeof bootstrap !== 'undefined') {
                var tab = new bootstrap.Tab(tabButton);
                tab.show();
            }

            document.querySelectorAll('#verificationTabs button[data-bs-toggle="tab"]').forEach(function (button) {
                button.addEventListener('shown.bs.tab', function () {
                    activeTab.value = button.getAttribute('data-bs-target').replace('#', '');
                });
            });
        });
    </script>
@endsection
