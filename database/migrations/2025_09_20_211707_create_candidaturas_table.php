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
        Schema::create('candidaturas', function (Blueprint $table) {
        $table->id();
        $table->string('nome');
        $table->string('email');
        $table->string('telefone', 50);
        $table->string('cargo');
        $table->string('escolaridade', 50);
        $table->text('observacoes')->nullable(); // único campo opcional
        $table->string('curriculo_path');        // onde o arquivo fica salvo
        $table->string('curriculo_original')->nullable(); // nome original (opcional)
        $table->string('ip', 45);                // suporta IPv6
        $table->timestamps();                    // created_at = data/hora do envio
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidaturas');
    }
};
