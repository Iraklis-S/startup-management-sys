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
        Schema::create('personat', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('birthplace', 150)->nullable();
            $table->string('affiliation_name', 255)->nullable();

            $table->enum('verification_status', ['pending', 'verified', 'rejected'])
                ->default('pending')
                ;
            $table->timestamp('verified_at')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->text('verification_note')->nullable();

            $table->timestamps();

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
        Schema::dropIfExists('personat');
    }
};
