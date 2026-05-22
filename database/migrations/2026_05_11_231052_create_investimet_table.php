<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('investimet', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('funding_round_id');
            $table->unsignedBigInteger('funded_company_id');
            $table->unsignedBigInteger('investor_company_id');
            $table->timestamps();


            $table->enum('verification_status', ['pending', 'verified', 'rejected', 'flagged'])
                ->default('pending');

            $table->unsignedBigInteger('verified_by')->nullable();

            $table->timestamp('verified_at')->nullable();

            $table->text('verification_note')->nullable();

            $table->foreign('verified_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('funding_round_id')
                ->references('id')
                ->on('raundet_financimit')
                ->cascadeOnDelete();

            $table->foreign('funded_company_id')
                ->references('id')
                ->on('kompanite');

            $table->foreign('investor_company_id')
                ->references('id')
                ->on('kompanite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('investimet');
    }
};
