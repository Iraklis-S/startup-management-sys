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
        Schema::create('ipos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->decimal('valuation_amount', 18, 2)->nullable();
            $table->string('valuation_currency_code', 10)->nullable();
            $table->decimal('raised_amount', 18, 2)->nullable();
            $table->string('raised_currency_code', 10)->nullable();
            $table->date('public_at')->nullable();
            $table->string('stock_symbol', 50)->nullable();
            $table->string('source_url', 500)->nullable();
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
        Schema::dropIfExists('ipos');
    }
};
