<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inscription_id')->constrained()->cascadeOnDelete();
            $table->string('numero_recu')->unique(); // ex: FOLEM2024-000123
            $table->string('chemin_pdf');             // chemin storage/app/public/recus/...
            $table->string('hash_verification', 40);  // pour QR code de contrôle d'accès le jour J
            $table->timestamp('genere_le');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recus');
    }
};
