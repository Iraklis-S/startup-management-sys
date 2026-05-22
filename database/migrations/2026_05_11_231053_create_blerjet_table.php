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
        Schema::create('blerjet', function (Blueprint $table) {
            $table->id();
            $table->string('acquisition_id', 100)->nullable();
            $table->unsignedBigInteger('acquiring_company_id');
            $table->unsignedBigInteger('acquired_company_id');
            $table->string('term_code', 50)->nullable();
            $table->decimal('price_amount', 18, 2)->nullable();
            $table->string('price_currency_code', 10)->nullable();
            $table->date('acquired_at')->nullable();
            $table->string('source_url', 500)->nullable();
            $table->text('source_description')->nullable();
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

            $table->foreign('acquiring_company_id')
                ->references('id')
                ->on('kompanite');

            $table->foreign('acquired_company_id')
                ->references('id')
                ->on('kompanite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blerjet');
    }
};
