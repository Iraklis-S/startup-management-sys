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
        Schema::create('kompanite', function (Blueprint $table) {
            $table->id();
            // legacy entity type/id removed; use `company_type` for classification
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->string('name', 255);
            $table->string('normalized_name', 255)->nullable();
            $table->string('permalink', 255)->nullable()->unique();
            $table->string('category_code', 100)->nullable();
            $table->string('status', 50)->nullable();
            $table->date('founded_at')->nullable();


            $table->enum('company_type', ['startup', 'other'])
                ->default('other');

            $table->enum('verification_status', ['pending', 'verified', 'rejected'])
                ->default('pending');
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_note')->nullable();

            $table->unsignedBigInteger('verified_by')
                ->nullable();

            $table->timestamps();
            
            $table->foreign('verified_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('parent_id')
                ->references('id')
                ->on('kompanite')
                ->nullOnDelete();


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kompanite');
    }
};
