<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('editions', function (Blueprint $table) {
            $table->id();
            $table->string('nom');                       // ex: FOLEM 2024
            $table->string('slug')->unique();             // ex: folem-2024
            $table->string('theme')->nullable();
            $table->unsignedSmallInteger('annee');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->string('lieu')->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();

            // Une seule édition "active" à la fois (inscriptions ouvertes)
            $table->enum('statut', ['draft', 'active', 'archived'])->default('draft');

            // Tarification (en XOF) - peut être surchargée par type de participant si besoin plus tard
            $table->unsignedInteger('montant_inscription')->default(0);

            $table->timestamps();

            $table->index('statut');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('editions');
    }
};
