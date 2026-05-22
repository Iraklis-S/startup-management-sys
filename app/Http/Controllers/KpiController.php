<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KpiController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'kpi' => $request->input('kpi'),
            'year' => $request->filled('year') ? (int) $request->input('year') : null,
            'category_code' => $request->filled('category_code') ? trim($request->input('category_code')) : null,
            'funding_round_type' => $request->filled('funding_round_type') ? trim($request->input('funding_round_type')) : null,
        ];

        $kpiOptions = [
            'funding_by_year' => 'Financimi sipas vitit',
            'funding_by_sector' => 'Financimi sipas sektorit',
            'rounds_by_type' => 'Raundet sipas llojit',
            'top_investors' => 'Top 10 investitorët sipas numrit të investimeve',
            'top_startups_by_funding' => 'Top 10 startup-et sipas kapitalit të mbledhur',
            'acquisitions_by_year' => 'Akuizimet sipas vitit',
            'ipos_by_year' => 'IPO sipas vitit',
            'offices_by_city' => 'Shpërndarja e zyrave sipas qytetit',
            'active_people' => 'Personat me më shumë marrëdhënie aktive',
            'top_funds' => 'Fondet me kapitalin më të lartë të ngritur',
        ];

        $cards = $this->getSummaryCards($filters);

        $chart = null;
        if (!empty($filters['kpi'])) {
            $chart = $this->getChartData($filters['kpi'], $filters);
        }

        return view('kpi.index', compact('filters', 'kpiOptions', 'cards', 'chart'));
    }

    private function getSummaryCards(array $filters): array
    {
        $sql = "SELECT COUNT(*) AS total
                FROM kompanite k
                WHERE k.company_type = 'startup'";
        $bindings = [];

        if (!empty($filters['year'])) {
            $sql .= " AND k.founded_at IS NOT NULL AND YEAR(k.founded_at) = ?";
            $bindings[] = $filters['year'];
        }

        if (!empty($filters['category_code'])) {
            $sql .= " AND k.category_code = ?";
            $bindings[] = $filters['category_code'];
        }

        $totalStartups = (int) (DB::selectOne($sql, $bindings)->total ?? 0);

        $sql = "SELECT SUM(CASE WHEN r.raised_amount_usd IS NOT NULL THEN r.raised_amount_usd WHEN r.raised_amount IS NOT NULL THEN r.raised_amount ELSE 0 END) AS total
                FROM raundet_financimit r
                INNER JOIN kompanite k ON k.id = r.company_id
                WHERE k.company_type = 'startup'";
        $bindings = [];

        if (!empty($filters['year'])) {
            $sql .= " AND r.funded_at IS NOT NULL AND YEAR(r.funded_at) = ?";
            $bindings[] = $filters['year'];
        }

        if (!empty($filters['category_code'])) {
            $sql .= " AND k.category_code = ?";
            $bindings[] = $filters['category_code'];
        }

        if (!empty($filters['funding_round_type'])) {
            $sql .= " AND r.funding_round_type = ?";
            $bindings[] = $filters['funding_round_type'];
        }

        $totalFunding = (float) (DB::selectOne($sql, $bindings)->total ?? 0);

        $sql = "SELECT COUNT(*) AS total
                FROM ipos i
                INNER JOIN kompanite k ON k.id = i.company_id
                WHERE k.company_type = 'startup'";
        $bindings = [];

        if (!empty($filters['year'])) {
            $sql .= " AND i.public_at IS NOT NULL AND YEAR(i.public_at) = ?";
            $bindings[] = $filters['year'];
        }

        if (!empty($filters['category_code'])) {
            $sql .= " AND k.category_code = ?";
            $bindings[] = $filters['category_code'];
        }

        $totalIpo = (int) (DB::selectOne($sql, $bindings)->total ?? 0);

        return [
            'total_startups' => $totalStartups,
            'total_funding' => $totalFunding,
            'total_ipos' => $totalIpo,
        ];
    }

    private function getChartData(string $kpi, array $filters): array
    {
        $sql = '';
        $bindings = [];
        $title = '';
        $type = 'bar';

        switch ($kpi) {
            case 'funding_by_year':
                if (!empty($filters['year'])) {
                    $title = 'Top 10 startup-et sipas financimit në vitin ' . $filters['year'];
                    $type = 'bar';

                    $sql = "SELECT CASE WHEN k.name IS NOT NULL THEN k.name ELSE CONCAT('Startup #', k.id) END AS label,
                                   SUM(CASE WHEN r.raised_amount_usd IS NOT NULL THEN r.raised_amount_usd WHEN r.raised_amount IS NOT NULL THEN r.raised_amount ELSE 0 END) AS value
                            FROM kompanite k
                            INNER JOIN raundet_financimit r ON r.company_id = k.id
                            WHERE k.company_type = 'startup' AND r.funded_at IS NOT NULL
                              AND YEAR(r.funded_at) = ?";
                    $bindings[] = $filters['year'];

                    if (!empty($filters['category_code'])) {
                        $sql .= " AND k.category_code = ?";
                        $bindings[] = $filters['category_code'];
                    }

                    if (!empty($filters['funding_round_type'])) {
                        $sql .= " AND r.funding_round_type = ?";
                        $bindings[] = $filters['funding_round_type'];
                    }

                    $sql .= " GROUP BY k.id, k.name
                              ORDER BY value DESC
                              LIMIT 10";
                } else {
                    $title = 'Financimi sipas vitit';
                    $type = 'line';

                    $sql = "SELECT YEAR(r.funded_at) AS label,
                                   SUM(CASE WHEN r.raised_amount_usd IS NOT NULL THEN r.raised_amount_usd WHEN r.raised_amount IS NOT NULL THEN r.raised_amount ELSE 0 END) AS value
                            FROM raundet_financimit r
                            INNER JOIN kompanite k ON k.id = r.company_id
                            WHERE k.company_type = 'startup' AND r.funded_at IS NOT NULL";

                    if (!empty($filters['category_code'])) {
                        $sql .= " AND k.category_code = ?";
                        $bindings[] = $filters['category_code'];
                    }

                    if (!empty($filters['funding_round_type'])) {
                        $sql .= " AND r.funding_round_type = ?";
                        $bindings[] = $filters['funding_round_type'];
                    }

                    $sql .= " GROUP BY YEAR(r.funded_at)
                              ORDER BY YEAR(r.funded_at)";
                }
                break;

            case 'funding_by_sector':
                $title = !empty($filters['year'])
                    ? 'Financimi sipas sektorit për vitin ' . $filters['year']
                    : 'Totali i kapitalit sipas sektorit';

                $type = 'bar';

                $sql = "SELECT CASE WHEN k.category_code IS NOT NULL THEN k.category_code ELSE 'Pa kategori' END AS label,
                               SUM(CASE WHEN r.raised_amount_usd IS NOT NULL THEN r.raised_amount_usd WHEN r.raised_amount IS NOT NULL THEN r.raised_amount ELSE 0 END) AS value
                        FROM raundet_financimit r
                        INNER JOIN kompanite k ON k.id = r.company_id
                        WHERE k.company_type = 'startup'";

                if (!empty($filters['year'])) {
                    $sql .= " AND r.funded_at IS NOT NULL AND YEAR(r.funded_at) = ?";
                    $bindings[] = $filters['year'];
                }

                if (!empty($filters['category_code'])) {
                    $sql .= " AND k.category_code = ?";
                    $bindings[] = $filters['category_code'];
                }

                if (!empty($filters['funding_round_type'])) {
                    $sql .= " AND r.funding_round_type = ?";
                    $bindings[] = $filters['funding_round_type'];
                }

                $sql .= " GROUP BY k.category_code
                          ORDER BY value DESC
                          LIMIT 10";
                break;

            case 'rounds_by_type':
                $title = !empty($filters['year'])
                    ? 'Raundet sipas llojit për vitin ' . $filters['year']
                    : 'Numri i raundeve sipas llojit';

                $type = 'doughnut';

                $sql = "SELECT CASE WHEN r.funding_round_type IS NOT NULL THEN r.funding_round_type ELSE 'Pa tip' END AS label,
                               COUNT(*) AS value
                        FROM raundet_financimit r
                        INNER JOIN kompanite k ON k.id = r.company_id
                        WHERE k.company_type = 'startup'";

                if (!empty($filters['year'])) {
                    $sql .= " AND r.funded_at IS NOT NULL AND YEAR(r.funded_at) = ?";
                    $bindings[] = $filters['year'];
                }

                if (!empty($filters['category_code'])) {
                    $sql .= " AND k.category_code = ?";
                    $bindings[] = $filters['category_code'];
                }

                if (!empty($filters['funding_round_type'])) {
                    $sql .= " AND r.funding_round_type = ?";
                    $bindings[] = $filters['funding_round_type'];
                }

                $sql .= " GROUP BY r.funding_round_type
                          ORDER BY value DESC";
                break;

            case 'top_investors':
                $title = !empty($filters['year'])
                    ? 'Top 10 investitorët për vitin ' . $filters['year']
                    : 'Top 10 investitorët sipas numrit të investimeve';

                $type = 'bar';

                $sql = "SELECT CASE WHEN inv.name IS NOT NULL THEN inv.name ELSE CONCAT('Investor #', i.investor_company_id) END AS label,
                               COUNT(*) AS value
                        FROM investimet i
                        INNER JOIN kompanite inv ON inv.id = i.investor_company_id
                        INNER JOIN kompanite funded ON funded.id = i.funded_company_id
                        INNER JOIN raundet_financimit r ON r.id = i.funding_round_id
                        WHERE funded.company_type = 'startup'";

                if (!empty($filters['year'])) {
                    $sql .= " AND r.funded_at IS NOT NULL AND YEAR(r.funded_at) = ?";
                    $bindings[] = $filters['year'];
                }

                if (!empty($filters['category_code'])) {
                    $sql .= " AND funded.category_code = ?";
                    $bindings[] = $filters['category_code'];
                }

                if (!empty($filters['funding_round_type'])) {
                    $sql .= " AND r.funding_round_type = ?";
                    $bindings[] = $filters['funding_round_type'];
                }

                $sql .= " GROUP BY i.investor_company_id, inv.name
                          ORDER BY value DESC
                          LIMIT 10";
                break;

            case 'top_startups_by_funding':
                $title = !empty($filters['year'])
                    ? 'Top 10 startup-et sipas kapitalit në vitin ' . $filters['year']
                    : 'Top 10 startup-et sipas kapitalit të mbledhur';

                $type = 'bar';

                $sql = "SELECT CASE WHEN k.name IS NOT NULL THEN k.name ELSE CONCAT('Startup #', k.id) END AS label,
                               SUM(CASE WHEN r.raised_amount_usd IS NOT NULL THEN r.raised_amount_usd WHEN r.raised_amount IS NOT NULL THEN r.raised_amount ELSE 0 END) AS value
                        FROM kompanite k
                        INNER JOIN raundet_financimit r ON r.company_id = k.id
                        WHERE k.company_type = 'startup'";

                if (!empty($filters['year'])) {
                    $sql .= " AND r.funded_at IS NOT NULL AND YEAR(r.funded_at) = ?";
                    $bindings[] = $filters['year'];
                }

                if (!empty($filters['category_code'])) {
                    $sql .= " AND k.category_code = ?";
                    $bindings[] = $filters['category_code'];
                }

                if (!empty($filters['funding_round_type'])) {
                    $sql .= " AND r.funding_round_type = ?";
                    $bindings[] = $filters['funding_round_type'];
                }

                $sql .= " GROUP BY k.id, k.name
                          ORDER BY value DESC
                          LIMIT 10";
                break;

            case 'acquisitions_by_year':
                if (!empty($filters['year'])) {
                    $title = 'Top 10 blerësit sipas akuizimeve në vitin ' . $filters['year'];
                    $type = 'bar';

                    $sql = "SELECT CASE WHEN acq.name IS NOT NULL THEN acq.name ELSE CONCAT('Company #', b.acquiring_company_id) END AS label,
                                   COUNT(*) AS value
                            FROM blerjet b
                            INNER JOIN kompanite acq ON acq.id = b.acquiring_company_id
                            INNER JOIN kompanite acquired_company ON acquired_company.id = b.acquired_company_id
                            WHERE acquired_company.company_type = 'startup' AND b.acquired_at IS NOT NULL
                              AND YEAR(b.acquired_at) = ?";
                    $bindings[] = $filters['year'];

                    if (!empty($filters['category_code'])) {
                        $sql .= " AND acquired_company.category_code = ?";
                        $bindings[] = $filters['category_code'];
                    }

                    $sql .= " GROUP BY b.acquiring_company_id, acq.name
                              ORDER BY value DESC
                              LIMIT 10";
                } else {
                    $title = 'Numri i akuizimeve sipas vitit';
                    $type = 'line';

                    $sql = "SELECT YEAR(b.acquired_at) AS label,
                                   COUNT(*) AS value
                            FROM blerjet b
                            INNER JOIN kompanite acquired_company ON acquired_company.id = b.acquired_company_id
                            WHERE acquired_company.company_type = 'startup' AND b.acquired_at IS NOT NULL";

                    if (!empty($filters['category_code'])) {
                        $sql .= " AND acquired_company.category_code = ?";
                        $bindings[] = $filters['category_code'];
                    }

                    $sql .= " GROUP BY YEAR(b.acquired_at)
                              ORDER BY YEAR(b.acquired_at)";
                }
                break;

            case 'ipos_by_year':
                if (!empty($filters['year'])) {
                    $title = 'Top 10 IPO në vitin ' . $filters['year'];
                    $type = 'bar';

                    $sql = "SELECT CASE WHEN k.name IS NOT NULL THEN k.name ELSE CONCAT('Company #', k.id) END AS label,
                                   CASE WHEN i.raised_amount IS NOT NULL THEN i.raised_amount WHEN i.valuation_amount IS NOT NULL THEN i.valuation_amount ELSE 0 END AS value
                            FROM ipos i
                            INNER JOIN kompanite k ON k.id = i.company_id
                            WHERE k.company_type = 'startup' AND i.public_at IS NOT NULL
                              AND YEAR(i.public_at) = ?";
                    $bindings[] = $filters['year'];

                    if (!empty($filters['category_code'])) {
                        $sql .= " AND k.category_code = ?";
                        $bindings[] = $filters['category_code'];
                    }

                    $sql .= " ORDER BY value DESC
                              LIMIT 10";
                } else {
                    $title = 'Numri i IPO-ve sipas vitit';
                    $type = 'line';

                    $sql = "SELECT YEAR(i.public_at) AS label,
                                   COUNT(*) AS value
                            FROM ipos i
                            INNER JOIN kompanite k ON k.id = i.company_id
                            WHERE k.company_type = 'startup' AND i.public_at IS NOT NULL";

                    if (!empty($filters['category_code'])) {
                        $sql .= " AND k.category_code = ?";
                        $bindings[] = $filters['category_code'];
                    }

                    $sql .= " GROUP BY YEAR(i.public_at)
                              ORDER BY YEAR(i.public_at)";
                }
                break;

            case 'offices_by_city':
                $title = !empty($filters['year'])
                    ? 'Shpërndarja e zyrave sipas qytetit për vitin ' . $filters['year']
                    : 'Shpërndarja e zyrave sipas qytetit';

                $type = 'bar';

                $sql = "SELECT CASE WHEN z.city IS NOT NULL THEN z.city ELSE 'Pa qytet' END AS label,
                               COUNT(*) AS value
                        FROM zyrat z
                        INNER JOIN kompanite k ON k.id = z.company_id
                        WHERE k.company_type = 'startup'";

                if (!empty($filters['year'])) {
                    $sql .= " AND k.founded_at IS NOT NULL AND YEAR(k.founded_at) = ?";
                    $bindings[] = $filters['year'];
                }

                if (!empty($filters['category_code'])) {
                    $sql .= " AND k.category_code = ?";
                    $bindings[] = $filters['category_code'];
                }

                $sql .= " GROUP BY z.city
                          ORDER BY value DESC
                          LIMIT 10";
                break;

            case 'active_people':
                $title = !empty($filters['year'])
                    ? 'Personat me më shumë marrëdhënie aktive në vitin ' . $filters['year']
                    : 'Personat me më shumë marrëdhënie aktive';

                $type = 'bar';

                $sql = "SELECT CASE WHEN TRIM(CONCAT(CASE WHEN p.first_name IS NOT NULL THEN p.first_name ELSE '' END, ' ', CASE WHEN p.last_name IS NOT NULL THEN p.last_name ELSE '' END)) = '' THEN CONCAT('Person #', p.id) ELSE TRIM(CONCAT(CASE WHEN p.first_name IS NOT NULL THEN p.first_name ELSE '' END, ' ', CASE WHEN p.last_name IS NOT NULL THEN p.last_name ELSE '' END)) END AS label,
                               COUNT(*) AS value
                        FROM marredheniet m
                        INNER JOIN personat p ON p.company_id = m.person_id
                        INNER JOIN kompanite k ON k.id = m.company_id
                        WHERE k.company_type = 'startup' AND (m.is_past IS NULL OR m.is_past = 0)";

                if (!empty($filters['year'])) {
                    $sql .= " AND (
                                (m.start_at IS NOT NULL AND YEAR(m.start_at) <= ?)
                                AND
                                (m.end_at IS NULL OR YEAR(m.end_at) >= ?)
                              )";
                    $bindings[] = $filters['year'];
                    $bindings[] = $filters['year'];
                }

                if (!empty($filters['category_code'])) {
                    $sql .= " AND k.category_code = ?";
                    $bindings[] = $filters['category_code'];
                }

                $sql .= " GROUP BY p.id, p.first_name, p.last_name
                          ORDER BY value DESC
                          LIMIT 10";
                break;

            case 'top_funds':
                $title = !empty($filters['year'])
                    ? 'Fondet me kapitalin më të lartë për vitin ' . $filters['year']
                    : 'Fondet me kapitalin më të lartë të ngritur';

                $type = 'bar';

                $sql = "SELECT CASE WHEN f.name IS NOT NULL THEN f.name WHEN k.name IS NOT NULL THEN k.name ELSE CONCAT('Fund #', f.id) END AS label,
                               SUM(CASE WHEN f.raised_amount IS NOT NULL THEN f.raised_amount ELSE 0 END) AS value
                        FROM fondet f
                        INNER JOIN kompanite k ON k.id = f.company_id
                        WHERE k.company_type = 'other'";

                if (!empty($filters['year'])) {
                    $sql .= " AND f.funded_at IS NOT NULL AND YEAR(f.funded_at) = ?";
                    $bindings[] = $filters['year'];
                }

                if (!empty($filters['category_code'])) {
                    $sql .= " AND k.category_code = ?";
                    $bindings[] = $filters['category_code'];
                }

                $sql .= " GROUP BY f.id, f.name, k.name
                          ORDER BY value DESC
                          LIMIT 10";
                break;

            default:
                return [
                    'title' => 'KPI i panjohur',
                    'type' => 'bar',
                    'labels' => [],
                    'values' => [],
                    'rows' => [],
                ];
        }

        $rows = DB::select($sql, $bindings);

        return [
            'title' => $title,
            'type' => $type,
            'labels' => array_map(fn ($row) => (string) $row->label, $rows),
            'values' => array_map(fn ($row) => (float) $row->value, $rows),
            'rows' => array_map(function ($row) {
                return [
                    'label' => (string) $row->label,
                    'value' => (float) $row->value,
                ];
            }, $rows),
        ];
    }
}