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
        Schema::table('payments', function (Blueprint $table) {
            // Só adiciona 'status' se não existir
            if (!Schema::hasColumn('payments', 'status')) {
                $table->string('status')->default('pendente')->after('amount');
            }

            // Só adiciona 'confirmed_by' se não existir
            if (!Schema::hasColumn('payments', 'confirmed_by')) {
                $table->foreignId('confirmed_by')->nullable()->constrained('users');
            }

            // Só adiciona 'confirmed_at' se não existir
            if (!Schema::hasColumn('payments', 'confirmed_at')) {
                $table->timestamp('confirmed_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            //
        });
    }
};
