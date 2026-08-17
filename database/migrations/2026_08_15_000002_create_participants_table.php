<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('email')->index();
            $table->string('telephone')->index(); // format 226XXXXXXXX, utilisé pour LigdiCash
            $table->string('structure')->nullable();
            $table->string('fonction')->nullable();
            $table->string('secteur_activite')->nullable(); // Administration/Privé/ONG/Association/Entrepreneur/Institution
            $table->string('pays')->default('Burkina Faso');
            $table->string('ville')->nullable();

            // Compte optionnel : un participant peut s'inscrire sans compte (via code),
            // ou créer un compte pour retrouver ses inscriptions passées.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();

            $table->unique(['email', 'telephone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
