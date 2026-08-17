<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partenaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edition_id')->constrained()->cascadeOnDelete();
            $table->string('nom');
            $table->string('logo');
            $table->enum('type', ['partenaire', 'sponsor'])->default('partenaire');
            $table->string('niveau')->nullable(); // Platine / Or / Argent / Bronze
            $table->string('lien_site')->nullable();
            $table->unsignedSmallInteger('ordre')->default(0);
            $table->timestamps();
        });

        Schema::create('pays_attendus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('edition_id')->constrained()->cascadeOnDelete();
            $table->string('nom_pays');
            $table->string('drapeau')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pays_attendus');
        Schema::dropIfExists('partenaires');
    }
};
