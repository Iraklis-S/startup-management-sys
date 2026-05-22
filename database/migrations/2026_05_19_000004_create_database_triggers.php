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
     * Creates database triggers to maintain data integrity and automate
     * verification workflows and validation logic.
     */
    public function up(): void
    {
        // Trigger: Maintain verification_status consistency on kompanite
        if (Schema::hasColumn('kompanite', 'verification_status')) {
            DB::statement(<<<'SQL'
                CREATE TRIGGER kompanite_verification_update
                BEFORE UPDATE ON kompanite
                FOR EACH ROW
                BEGIN
                    -- If being marked as verified but verified_at is null, set it
                    IF NEW.verification_status = 'verified' AND NEW.verified_at IS NULL THEN
                        SET NEW.verified_at = NOW();
                    END IF;

                    -- If being marked as not verified, clear verification metadata
                    IF NEW.verification_status != 'verified' THEN
                        SET NEW.verified_by = NULL;
                    END IF;
                END
            SQL);
        }

        // Trigger: Maintain verification_status consistency on personat
        if (Schema::hasColumn('personat', 'verification_status')) {
            DB::statement(<<<'SQL'
                CREATE TRIGGER personat_verification_update
                BEFORE UPDATE ON personat
                FOR EACH ROW
                BEGIN
                    IF NEW.verification_status = 'verified' AND NEW.verified_at IS NULL THEN
                        SET NEW.verified_at = NOW();
                    END IF;

                    IF NEW.verification_status != 'verified' THEN
                        SET NEW.verified_by = NULL;
                    END IF;
                END
            SQL);
        }

        // Trigger: Maintain verification_status consistency on raundet_financimit
        if (Schema::hasColumn('raundet_financimit', 'verification_status')) {
            DB::statement(<<<'SQL'
                CREATE TRIGGER raundet_financimit_verification_update
                BEFORE UPDATE ON raundet_financimit
                FOR EACH ROW
                BEGIN
                    IF NEW.verification_status = 'verified' AND NEW.verified_at IS NULL THEN
                        SET NEW.verified_at = NOW();
                    END IF;

                    IF NEW.verification_status != 'verified' THEN
                        SET NEW.verified_by = NULL;
                    END IF;
                END
            SQL);
        }

        // Trigger: Validate marredheniet date logic (end_at >= start_at)
        if (Schema::hasColumn('marredheniet', 'start_at')) {
            DB::statement(<<<'SQL'
                CREATE TRIGGER marredheniet_date_validation
                BEFORE INSERT ON marredheniet
                FOR EACH ROW
                BEGIN
                    IF NEW.end_at IS NOT NULL AND NEW.end_at < NEW.start_at THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Relationship end_at cannot be before start_at';
                    END IF;

                    -- Auto-derive is_past based on end_at and current date
                    IF NEW.end_at IS NOT NULL AND NEW.end_at <= CURDATE() THEN
                        SET NEW.is_past = 1;
                    ELSE
                        SET NEW.is_past = 0;
                    END IF;
                END
            SQL);

            DB::statement(<<<'SQL'
                CREATE TRIGGER marredheniet_date_validation_update
                BEFORE UPDATE ON marredheniet
                FOR EACH ROW
                BEGIN
                    IF NEW.end_at IS NOT NULL AND NEW.end_at < NEW.start_at THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Relationship end_at cannot be before start_at';
                    END IF;

                    -- Auto-derive is_past based on end_at and current date
                    IF NEW.end_at IS NOT NULL AND NEW.end_at <= CURDATE() THEN
                        SET NEW.is_past = 1;
                    ELSE
                        SET NEW.is_past = 0;
                    END IF;
                END
            SQL);
        }

        // Trigger: Update updated_at timestamp on kompanite changes
        DB::statement(<<<'SQL'
            CREATE TRIGGER kompanite_update_timestamp
            BEFORE UPDATE ON kompanite
            FOR EACH ROW
            SET NEW.updated_at = NOW()
        SQL);

        // Trigger: Update updated_at timestamp on personat changes
        DB::statement(<<<'SQL'
            CREATE TRIGGER personat_update_timestamp
            BEFORE UPDATE ON personat
            FOR EACH ROW
            SET NEW.updated_at = NOW()
        SQL);

        // Trigger: Update updated_at timestamp on raundet_financimit changes
        DB::statement(<<<'SQL'
            CREATE TRIGGER raundet_financimit_update_timestamp
            BEFORE UPDATE ON raundet_financimit
            FOR EACH ROW
            SET NEW.updated_at = NOW()
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS kompanite_verification_update');
        DB::statement('DROP TRIGGER IF EXISTS personat_verification_update');
        DB::statement('DROP TRIGGER IF EXISTS raundet_financimit_verification_update');
        DB::statement('DROP TRIGGER IF EXISTS marredheniet_date_validation');
        DB::statement('DROP TRIGGER IF EXISTS marredheniet_date_validation_update');
        DB::statement('DROP TRIGGER IF EXISTS kompanite_update_timestamp');
        DB::statement('DROP TRIGGER IF EXISTS personat_update_timestamp');
        DB::statement('DROP TRIGGER IF EXISTS raundet_financimit_update_timestamp');
    }
};
