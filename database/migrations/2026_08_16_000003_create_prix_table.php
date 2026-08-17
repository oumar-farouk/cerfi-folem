<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prix', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edition_id')->constrained()->cascadeOnDelete();
            $table->foreignId('region_id')->constrained()->cascadeOnDelete();
            $table->foreignId('profil_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('montant');
            $table->timestamps();

            // Un seul tarif par combinaison édition + région + profil
            $table->unique(['edition_id', 'region_id', 'profil_id'], 'prix_edition_region_profil_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prix');
    }
};
