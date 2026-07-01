<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Alteramos de ENUM para STRING para aceitar 'em_validacao' e outros futuros
            $table->string('status')->change();
        });

        Schema::table('payments', function (Blueprint $table) {
            // Aproveitamos para garantir que a tabela de pagamentos também aceita 'rejected'
            $table->string('status')->default('pending')->change();
        });
    }

    public function down(): void
    {
        // Caso precises de reverter, ele volta a ser o que era (ajusta se os teus enums eram diferentes)
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('status', ['pending', 'paid', 'partial', 'overdue'])->change();
        });
    }
};