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
        Schema::create('raundet_financimit', function (Blueprint $table) {
            $table->id();
            $table->string('funding_round_id', 100)->nullable();
            $table->unsignedBigInteger('company_id');
            $table->date('funded_at')->nullable();
            $table->string('funding_round_type', 100)->nullable();
            $table->string('funding_round_code', 50)->nullable();
            $table->decimal('raised_amount_usd', 18, 2)->nullable();
            $table->decimal('raised_amount', 18, 2)->nullable();
            $table->string('raised_currency_code', 10)->nullable();
            $table->decimal('pre_money_valuation_usd', 18, 2)->nullable();
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

            $table->foreign('company_id')
                ->references('id')
                ->on('kompanite')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('raundet_financimit');
    }
};
