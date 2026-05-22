<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasTable('rolet') && Schema::hasColumn('users', 'role_id')) {
                $table->foreign('role_id')
                    ->references('id')
                    ->on('rolet');
            }

            if (Schema::hasTable('personat') && Schema::hasColumn('users', 'person_id')) {
                $table->foreign('person_id')
                    ->references('id')
                    ->on('personat')
                    ->nullOnDelete();
            }

            if (Schema::hasTable('kompanite') && Schema::hasColumn('users', 'kompani_id')) {
                $table->foreign('kompani_id')
                    ->references('id')
                    ->on('kompanite')
                    ->nullOnDelete();
            }
        });

     
    }




    public function down(): void
    {
        Schema::table('kompanite', function (Blueprint $table) {
            if (Schema::hasColumn('kompanite', 'verified_by')) {
                $table->dropForeign(['verified_by']);
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'kompani_id')) {
                $table->dropForeign(['kompani_id']);
            }
            if (Schema::hasColumn('users', 'person_id')) {
                $table->dropForeign(['person_id']);
            }
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropForeign(['role_id']);
            }
        });
    }
};
