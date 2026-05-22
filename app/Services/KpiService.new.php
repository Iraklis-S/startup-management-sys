<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\StartupKpi;
use App\Models\InvestorHistory;
use App\Models\RoundDetail;

class KpiService
{
    public function startupsPerYear()
    {
        return StartupKpi::query()
            ->selectRaw('YEAR(founded_at) as year, COUNT(*) as total')
            ->where('company_type', 'startup')
            ->groupByRaw('YEAR(founded_at)')
            ->orderBy('year')
            ->get();
    }

    public function capitalBySector()
    {
        return StartupKpi::query()
            ->select('category_code', DB::raw('SUM(total_funding_usd) as total'))
            ->groupBy('category_code')
            ->orderByDesc('total')
            ->get();
    }

    public function roundsByType()
    {
        return DB::table('raundet_financimit')
            ->select('funding_round_type', DB::raw('COUNT(*) as total'))
            ->groupBy('funding_round_type')
            ->get();
    }

    public function top10Investors()
    {
        return InvestorHistory::query()
            ->selectRaw('investor_id, investor_name as name, COUNT(*) as investment_count')
            ->groupBy('investor_id', 'investor_name')
            ->orderByDesc('investment_count')
            ->limit(10)
            ->get();
    }

    public function top10CompaniesByFunding()
    {
        // Use the database function to compute total funding per company for accuracy and speed
        return DB::table('kompanite as k')
            ->select('k.name', DB::raw('total_funding_per_company(k.id) as total_raised'))
            ->orderByDesc('total_raised')
            ->limit(10)
            ->get();
    }

    public function acquisitionsPerYear()
    {
        return DB::table('blerjet')
            ->selectRaw('YEAR(acquired_at) as year, COUNT(*) as total')
            ->groupByRaw('YEAR(acquired_at)')
            ->get();
    }

    public function iposPerYear()
    {
        return DB::table('ipos')
            ->selectRaw('YEAR(public_at) as year, COUNT(*) as total')
            ->groupByRaw('YEAR(public_at)')
            ->get();
    }

    public function geographicDistribution()
    {
        return DB::table('zyrat')
            ->select('city', DB::raw('COUNT(*) as total'))
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderByDesc('total')
            ->get();
    }

    public function topPersonsByRelations()
    {
        return DB::table('marredheniet as m')
            ->join('personat as p', 'p.id', '=', 'm.person_id')
            ->selectRaw("CONCAT(p.first_name,' ',p.last_name) as name, COUNT(m.id) as rel_count")
            ->groupBy('m.person_id', 'p.first_name', 'p.last_name')
            ->orderByDesc('rel_count')
            ->limit(10)
            ->get();
    }

    public function topFundsByCapital()
    {
        return DB::table('fondet')
            ->select('name', 'raised_amount', 'raised_currency_code')
            ->orderByDesc('raised_amount')
            ->limit(10)
            ->get();
    }
}
