<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates strategic indexes to support search, filtering, KPI reporting,
     * relationship queries, and verification workflows.
     */
    public function up(): void
    {
        // Unique identifier indexes
        Schema::table('kompanite', function (Blueprint $table) {
            $table->unique('permalink', 'unique_permalink_on_kompanite');
        });

        // Composite indexes for filtering and dashboards
        Schema::table('kompanite', function (Blueprint $table) {
            $table->index(['category_code', 'status'], 'idx_kompanite_category_status');
        });

        // Funding and KPI indexes
        Schema::table('raundet_financimit', function (Blueprint $table) {
            $table->index(['company_id', 'funded_at'], 'idx_raundetfinancimit_companyid_fundedat');
        });

        // Investment history indexes
        Schema::table('investimet', function (Blueprint $table) {
            $table->index(['funded_company_id', 'funding_round_id'], 'idx_investimet_funded_round');
            $table->index(['investor_company_id', 'funding_round_id'], 'idx_investimet_investor_round');
        });

        // Relationship lookup indexes
        Schema::table('marredheniet', function (Blueprint $table) {
            $table->index(['company_id', 'is_past', 'title'], 'idx_marredheniet_company_role');
        });

        // Office location indexes
        Schema::table('zyrat', function (Blueprint $table) {
            $table->index(['company_id', 'city'], 'idx_zyrat_company_city');
        });

        // Verification workflow indexes (apply to all verification-enabled tables)
        foreach (['kompanite', 'personat', 'raundet_financimit', 'investimet', 'fondet', 'blerjet', 'ipos', 'arritjet', 'marredheniet', 'zyrat', 'edukimet'] as $tableName) {
            if (Schema::hasTable($tableName)) {
                $hasVerificationStatus = Schema::hasColumn($tableName, 'verification_status');
                $hasVerifiedAt = Schema::hasColumn($tableName, 'verified_at');

                if ($hasVerificationStatus && $hasVerifiedAt) {
                    Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                        $table->index(
                            ['verification_status', 'verified_at'],
                            'idx_' . $tableName . '_verification_queue'
                        );
                    });
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kompanite', function (Blueprint $table) {
            $table->dropUnique('unique_permalink_on_kompanite');
            $table->dropIndex('idx_kompanite_category_status');
        });

        Schema::table('raundet_financimit', function (Blueprint $table) {
            $table->dropIndex('idx_raundetfinancimit_companyid_fundedat');
        });

        Schema::table('investimet', function (Blueprint $table) {
            $table->dropIndex('idx_investimet_funded_round');
            $table->dropIndex('idx_investimet_investor_round');
        });

        Schema::table('marredheniet', function (Blueprint $table) {
            $table->dropIndex('idx_marredheniet_company_role');
        });

        Schema::table('zyrat', function (Blueprint $table) {
            $table->dropIndex('idx_zyrat_company_city');
        });

        foreach (['kompanite', 'personat', 'raundet_financimit', 'investimet', 'fondet', 'blerjet', 'ipos', 'arritjet', 'marredheniet', 'zyrat', 'edukimet'] as $tableName) {
            if (Schema::hasTable($tableName)) {
                $indexName = 'idx_' . $tableName . '_verification_queue';
                DB::statement("ALTER TABLE {$tableName} DROP INDEX IF EXISTS {$indexName}");
            }
        }
    }
};
