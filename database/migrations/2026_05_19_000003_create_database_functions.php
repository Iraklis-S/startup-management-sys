<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates MySQL functions to support KPI calculations and common business logic queries.
     */
    public function up(): void
    {
        // Function: Total funding amount for a company
        DB::statement('DROP FUNCTION IF EXISTS total_funding_per_company');
        DB::statement(<<<'SQL'
            CREATE FUNCTION total_funding_per_company(p_company_id INT)
            RETURNS DECIMAL(20, 2)
            DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE total DECIMAL(20, 2);
                SELECT SUM(raised_amount_usd)
                INTO total
                FROM raundet_financimit
                WHERE company_id = p_company_id;
                IF total IS NULL THEN
                    SET total = 0;
                END IF;
                RETURN total;
            END
        SQL);

        // Function: Days between company founding and first funding round
        DB::statement('DROP FUNCTION IF EXISTS days_to_first_funding');
        DB::statement(<<<'SQL'
            CREATE FUNCTION days_to_first_funding(p_company_id INT)
            RETURNS INT
            DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE days INT;
                DECLARE first_funding_date DATE;
                DECLARE founded_date DATE;

                SELECT founded_at INTO founded_date FROM kompanite WHERE id = p_company_id LIMIT 1;
                SELECT MIN(funded_at) INTO first_funding_date FROM raundet_financimit WHERE company_id = p_company_id;

                IF first_funding_date IS NULL OR founded_date IS NULL THEN
                    RETURN NULL;
                END IF;

                SET days = DATEDIFF(first_funding_date, founded_date);
                RETURN days;
            END
        SQL);

        // Function: Total number of funding rounds for a company
        DB::statement('DROP FUNCTION IF EXISTS total_rounds_per_company');
        DB::statement(<<<'SQL'
            CREATE FUNCTION total_rounds_per_company(p_company_id INT)
            RETURNS INT
            DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE round_count INT;
                SELECT COUNT(DISTINCT id)
                INTO round_count
                FROM raundet_financimit
                WHERE company_id = p_company_id;
                IF round_count IS NULL THEN
                    RETURN 0;
                END IF;
                RETURN round_count;
            END
        SQL);

        // Function: Average funding per round for a company
        DB::statement('DROP FUNCTION IF EXISTS avg_funding_per_round');
        DB::statement(<<<'SQL'
            CREATE FUNCTION avg_funding_per_round(p_company_id INT)
            RETURNS DECIMAL(20, 2)
            DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE avg_amount DECIMAL(20, 2);
                SELECT AVG(raised_amount_usd)
                INTO avg_amount
                FROM raundet_financimit
                WHERE company_id = p_company_id;
                IF avg_amount IS NULL THEN
                    SET avg_amount = 0;
                END IF;
                RETURN avg_amount;
            END
        SQL);

        // Function: Total number of active investments (people in current roles)
        DB::statement('DROP FUNCTION IF EXISTS active_relationships_per_company');
        DB::statement(<<<'SQL'
            CREATE FUNCTION active_relationships_per_company(p_company_id INT)
            RETURNS INT
            DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE count INT;
                SELECT COUNT(*)
                INTO count
                FROM marredheniet
                WHERE company_id = p_company_id AND is_past = 0;
                IF count IS NULL THEN
                    RETURN 0;
                END IF;
                RETURN count;
            END
        SQL);

        // Function: Total number of office locations for a company
        DB::statement('DROP FUNCTION IF EXISTS office_count_per_company');
        DB::statement(<<<'SQL'
            CREATE FUNCTION office_count_per_company(p_company_id INT)
            RETURNS INT
            DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE count INT;
                SELECT COUNT(*)
                INTO count
                FROM zyrat
                WHERE company_id = p_company_id;
                IF count IS NULL THEN
                    RETURN 0;
                END IF;
                RETURN count;
            END
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP FUNCTION IF EXISTS total_funding_per_company');
        DB::statement('DROP FUNCTION IF EXISTS days_to_first_funding');
        DB::statement('DROP FUNCTION IF EXISTS total_rounds_per_company');
        DB::statement('DROP FUNCTION IF EXISTS avg_funding_per_round');
        DB::statement('DROP FUNCTION IF EXISTS active_relationships_per_company');
        DB::statement('DROP FUNCTION IF EXISTS office_count_per_company');
    }
};
