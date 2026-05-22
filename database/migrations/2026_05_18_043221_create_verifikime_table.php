<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('verifikime', function (Blueprint $table) {
            $table->id();
            $table->string('verifiable_type');
            $table->unsignedBigInteger('verifiable_id');
            $table->enum('action', ['approved','rejected','flagged']);
            $table->text('note')->nullable();
            $table->unsignedBigInteger('verified_by')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['verifiable_type', 'verifiable_id']);
            $table->foreign('verified_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verifikime');
    }
};
