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
        Schema::create('marredheniet', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('person_id');
            $table->unsignedBigInteger('company_id');
            $table->date('start_at')->nullable();
            $table->date('end_at')->nullable();
            $table->boolean('is_past')->default(false);
            $table->integer('sequence')->nullable();
            $table->string('title', 150)->nullable();
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

            $table->foreign('person_id')
                ->references('id')
                ->on('personat')
                ->cascadeOnDelete();

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
        Schema::dropIfExists('marredheniet');
    }
};
