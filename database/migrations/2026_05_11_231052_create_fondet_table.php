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
        Schema::create('fondet', function (Blueprint $table) {
            $table->id();
            $table->string('fund_id', 100)->nullable();
            $table->unsignedBigInteger('company_id');
            $table->string('name', 255)->nullable();
            $table->date('funded_at')->nullable();
            $table->decimal('raised_amount', 18, 2)->nullable();
            $table->string('raised_currency_code', 10)->nullable();
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
        Schema::dropIfExists('fondet');
    }
};
