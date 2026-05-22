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
        Schema::create('edukimet', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('person_id');
            $table->string('degree_type', 100)->nullable();
            $table->string('subject', 150)->nullable();
            $table->string('institution', 255)->nullable();
            $table->date('graduated_at')->nullable();
            $table->timestamps();

            $table->foreign('person_id')
                ->references('id')
                ->on('personat')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('edukimet');
    }
};
