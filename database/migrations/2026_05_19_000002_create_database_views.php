<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates database views to provide read models for reporting, KPI calculation,
     * investor analysis, and verification workflows.
     */
    public function up(): void
    {
        // Drop views if they exist (to allow fresh creation)
        DB::statement('DROP VIEW IF EXISTS company_relationships_view');
        DB::statement('DROP VIEW IF EXISTS round_detail_view');
        DB::statement('DROP VIEW IF EXISTS verification_queue_view');
        DB::statement('DROP VIEW IF EXISTS investor_history_view');
        DB::statement('DROP VIEW IF EXISTS startup_kpis_view');

        // View: Startup KPIs - Aggregated funding and round metrics per company
        DB::statement(<<<'SQL'
            CREATE VIEW startup_kpis_view AS
            SELECT
                k.id,
                k.name,
                k.company_type,
                k.category_code,
                k.status,
                k.founded_at,
                k.created_at,
                CASE WHEN SUM(r.raised_amount_usd) IS NULL THEN 0 ELSE SUM(r.raised_amount_usd) END AS total_funding_usd,
                COUNT(DISTINCT r.id) AS total_rounds,
                CASE WHEN AVG(r.raised_amount_usd) IS NULL THEN 0 ELSE AVG(r.raised_amount_usd) END AS avg_funding_per_round,
                MIN(r.funded_at) AS first_funding_date,
                MAX(r.funded_at) AS last_funding_date,
                DATEDIFF(
                    CASE WHEN MIN(r.funded_at) IS NOT NULL THEN MIN(r.funded_at) ELSE k.founded_at END,
                    k.founded_at
                ) AS days_to_first_funding
            FROM kompanite k
            LEFT JOIN raundet_financimit r ON k.id = r.company_id
            GROUP BY k.id, k.name, k.company_type, k.category_code, k.status, k.founded_at, k.created_at
        SQL);

        // View: Investor Portfolio - Who invested in which companies
        DB::statement(<<<'SQL'
            CREATE VIEW investor_history_view AS
            SELECT
                i.investor_company_id AS investor_id,
                inv.name AS investor_name,
                i.funded_company_id AS company_id,
                k.name AS company_name,
                r.funded_at,
                r.funding_round_type,
                r.raised_amount_usd,
                i.created_at AS investment_recorded_at
            FROM investimet i
            LEFT JOIN kompanite inv ON i.investor_company_id = inv.id
            LEFT JOIN kompanite k ON i.funded_company_id = k.id
            LEFT JOIN raundet_financimit r ON i.funding_round_id = r.id
            ORDER BY i.investor_company_id, r.funded_at DESC
        SQL);

        // View: Verification Queue - Only includes kompanite records
        DB::statement(<<<'SQL'
            CREATE VIEW verification_queue_view AS
            SELECT
                'kompanite' AS entity_type,
                id AS entity_id,
                name AS entity_name,
                CASE WHEN verification_status IS NOT NULL THEN verification_status ELSE 'pending' END AS verification_status,
                verified_by,
                verified_at,
                created_at,
                updated_at
            FROM kompanite
            WHERE (CASE WHEN verification_status IS NOT NULL THEN verification_status ELSE 'pending' END) != 'verified'
            ORDER BY created_at ASC
        SQL);

        // View: Funding Round Detail - Comprehensive round reporting with company context
        DB::statement(<<<'SQL'
            CREATE VIEW round_detail_view AS
            SELECT
                r.id,
                r.funding_round_id,
                k.id AS company_id,
                k.name AS company_name,
                k.category_code,
                k.status AS company_status,
                r.funded_at,
                r.funding_round_type,
                r.funding_round_code,
                r.raised_amount_usd,
                r.raised_amount,
                r.raised_currency_code,
                r.pre_money_valuation_usd,
                COUNT(DISTINCT i.investor_company_id) AS investor_count,
                GROUP_CONCAT(DISTINCT inv.name) AS investor_names,
                r.verification_status,
                r.created_at
            FROM raundet_financimit r
            LEFT JOIN kompanite k ON r.company_id = k.id
            LEFT JOIN investimet i ON r.id = i.funding_round_id
            LEFT JOIN kompanite inv ON i.investor_company_id = inv.id
            GROUP BY
                r.id, r.funding_round_id, k.id, k.name, k.category_code,
                k.status, r.funded_at, r.funding_round_type, r.funding_round_code,
                r.raised_amount_usd, r.raised_amount, r.raised_currency_code,
                r.pre_money_valuation_usd, r.verification_status, r.created_at
        SQL);

        // View: Company Relationship Summary - Current and past people-company roles
        DB::statement(<<<'SQL'
            CREATE VIEW company_relationships_view AS
            SELECT
                m.company_id AS company_id,
                k.name AS company_name,
                m.person_id,
                p.first_name,
                p.last_name,
                m.title,
                m.start_at,
                m.end_at,
                m.is_past,
                m.sequence,
                DATEDIFF(CASE WHEN m.end_at IS NOT NULL THEN m.end_at ELSE CURDATE() END, m.start_at) AS tenure_days,
                m.created_at
            FROM marredheniet m
            LEFT JOIN kompanite k ON m.company_id = k.id
            LEFT JOIN personat p ON m.person_id = p.id
            ORDER BY m.company_id, m.sequence
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS startup_kpis_view');
        DB::statement('DROP VIEW IF EXISTS investor_history_view');
        DB::statement('DROP VIEW IF EXISTS verification_queue_view');
        DB::statement('DROP VIEW IF EXISTS round_detail_view');
        DB::statement('DROP VIEW IF EXISTS company_relationships_view');
    }
};
