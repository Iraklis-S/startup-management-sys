@extends('layouts.app')

@section('title', 'KPI')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">KPI dhe Raporte</h1>
</div>

<form method="GET" action="{{ route('kpi.index') }}" class="row g-3 mb-4">
    <div class="col-md-3">
        <label class="form-label">KPI</label>
        <select class="form-select" name="kpi" required>
            <option value="">-- Zgjidh KPI --</option>
            @foreach($kpiOptions as $value => $label)
                <option value="{{ $value }}" {{ ($filters['kpi'] ?? '') === $value ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Viti</label>
        <input
            type="number"
            class="form-control"
            name="year"
            value="{{ $filters['year'] ?? '' }}"
            placeholder="p.sh. 2012"
        >
    </div>

    <div class="col-md-3">
        <label class="form-label">Sektori</label>
        <input
            type="text"
            class="form-control"
            name="category_code"
            value="{{ $filters['category_code'] ?? '' }}"
            placeholder="p.sh. software"
        >
    </div>

    <div class="col-md-3">
        <label class="form-label">Lloji i raundit</label>
        <input
            type="text"
            class="form-control"
            name="funding_round_type"
            value="{{ $filters['funding_round_type'] ?? '' }}"
            placeholder="p.sh. seed"
        >
    </div>

    <div class="col-md-3 d-flex align-items-end">
        <button class="btn btn-primary w-100">Gjenero KPI</button>
    </div>

    <div class="col-md-3 d-flex align-items-end">
        <a href="{{ route('kpi.index') }}" class="btn btn-outline-secondary w-100">Pastro filtrat</a>
    </div>
</form>

<div class="row g-3">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted">Total Startup-e</h6>
                <p class="fs-3 fw-bold mb-0">{{ number_format($cards['total_startups']) }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted">Total Funding</h6>
                <p class="fs-3 fw-bold mb-0">{{ number_format($cards['total_funding'], 2) }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">
                <h6 class="text-muted">Total IPO</h6>
                <p class="fs-3 fw-bold mb-0">{{ number_format($cards['total_ipos']) }}</p>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4 shadow-sm border-0">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Grafiku i KPI</span>
        @if(!empty($chart))
            <span class="badge bg-primary">{{ $chart['title'] }}</span>
        @endif
    </div>

    <div class="card-body">
        @if(empty($chart))
            <div class="alert alert-info mb-0">
                Zgjidh një KPI dhe kliko <strong>Gjenero KPI</strong> për të shfaqur vetëm atë grafik.
            </div>
        @elseif(count($chart['labels']) === 0)
            <div class="alert alert-warning mb-0">
                Nuk mund te gjenerohet nje chart i vlefshem per keto te dhena.
            </div>
        @else
            <div style="position: relative; min-height: 420px;">
                <canvas id="kpiChart"></canvas>
            </div>

            <hr class="my-4">

            <div class="table-responsive">
                <table class="table table-striped table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Label</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($chart['rows'] as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $row['label'] }}</td>
                                <td>{{ number_format($row['value'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@if(!empty($chart) && count($chart['labels']) > 0)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const payload = @json($chart);
    const canvas = document.getElementById('kpiChart');

    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    const colors = buildColors(payload.labels.length);

    new Chart(ctx, {
        type: payload.type,
        data: {
            labels: payload.labels,
            datasets: [{
                label: payload.title,
                data: payload.values,
                backgroundColor: payload.type === 'line'
                    ? 'rgba(13, 110, 253, 0.15)'
                    : colors.background,
                borderColor: payload.type === 'line'
                    ? 'rgba(13, 110, 253, 1)'
                    : colors.border,
                borderWidth: 2,
                fill: payload.type === 'line',
                tension: payload.type === 'line' ? 0.35 : 0.2,
                pointBackgroundColor: 'rgba(13, 110, 253, 1)',
                pointRadius: payload.type === 'line' ? 4 : 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: payload.type === 'doughnut'
                },
                title: {
                    display: true,
                    text: payload.title,
                    font: {
                        size: 18
                    }
                }
            },
            scales: payload.type === 'doughnut'
                ? {}
                : {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                }
        }
    });

    function buildColors(count) {
        const palette = [
            ['rgba(13,110,253,0.65)', 'rgba(13,110,253,1)'],
            ['rgba(25,135,84,0.65)', 'rgba(25,135,84,1)'],
            ['rgba(255,193,7,0.65)', 'rgba(255,193,7,1)'],
            ['rgba(220,53,69,0.65)', 'rgba(220,53,69,1)'],
            ['rgba(111,66,193,0.65)', 'rgba(111,66,193,1)'],
            ['rgba(13,202,240,0.65)', 'rgba(13,202,240,1)'],
            ['rgba(253,126,20,0.65)', 'rgba(253,126,20,1)'],
            ['rgba(108,117,125,0.65)', 'rgba(108,117,125,1)'],
            ['rgba(32,201,151,0.65)', 'rgba(32,201,151,1)'],
            ['rgba(214,51,132,0.65)', 'rgba(214,51,132,1)'],
        ];

        const background = [];
        const border = [];

        for (let i = 0; i < count; i++) {
            const item = palette[i % palette.length];
            background.push(item[0]);
            border.push(item[1]);
        }

        return { background, border };
    }
});
</script>
@endif
@endpush